<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveCard;
use App\Models\LeaveCardEntry;
use App\Models\LeaveApplication;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveType;
use Carbon\Carbon;

class LeaveCardController extends Controller
{
    /* ═══════════════════════════════════════════════
     * INDEX — employee list + panel scaffold
     * ═══════════════════════════════════════════════ */
    public function index()
    {
        $employees = Employee::with(['position', 'department'])
                             ->where('is_active', 1)
                             ->orderBy('last_name')
                             ->orderBy('first_name')
                             ->get();

        $departments = Department::orderBy('department_name')->get();
        $leaveTypes  = LeaveType::orderBy('type_name')->get();

        return view('leave_card.index', compact('employees', 'departments', 'leaveTypes'));
    }

    /* ═══════════════════════════════════════════════
     * SHOW — JSON payload for the editor panel
     *
     * MONTH DIVIDER RULE:
     *   Regular leaves  → grouped by application_date (filing date)
     *   Half-day leaves → grouped by date_of_absence (absence date)
     *
     *   Examples:
     *     Filed Mar 31, leave Apr 1–5  → March divider ✓
     *     Filed Apr 1,  leave Mar 28   → April divider ✓
     *     Half-day absence Mar 11      → March divider ✓
     *
     * HALF-DAY COLUMN MAPPING:
     *   Half-day (0.5 days) → tardy_undertime column
     *   This deducts from VL via the same formula recalcAll() uses.
     *   VL type  → tardy_undertime (deducts VL)
     *   SL type  → taken_sl (deducts SL) ← handled in autoImportApproved() frontend
     *
     * ALL STATUSES FETCHED:
     *   Both regular and half-day applications are fetched for ALL statuses.
     *   The frontend autoImportApproved() filters to APPROVED before inserting.
     *   Non-approved records are needed so excluded/tombstoned IDs can be
     *   properly tracked and re-recognised on reload.
     *
     * SORT ORDER:
     *   Both regular and half-day applications are sorted by created_at ASC
     *   after merging, so the sheet order reflects the actual order in which
     *   records were created in the DB — half-days will no longer pile up at
     *   the bottom after all regular leaves.
     * ═══════════════════════════════════════════════ */
    public function show(int $employeeId, int $year)
    {
        // FIX: Employee PK is user_id, but the route parameter is employee_id.
        // findOrFail() looks up by PK (user_id) so it always throws for a 6-digit
        // employee_id like 160675. Use where('employee_id') instead.
        $employee = Employee::with(['position', 'department'])
                            ->where('employee_id', $employeeId)
                            ->firstOrFail();

        // Identify the user_id for leave_card queries
        $userId = $employee->user_id;

        /* ── Live DB balance for the year ── */
        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 1)
                        ->where('year', $year)
                        ->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 2)
                        ->where('year', $year)
                        ->first();

        $currentVl = $vlBalance ? (float) $vlBalance->remaining_balance : null;
        $currentSl = $slBalance ? (float) $slBalance->remaining_balance : null;
        $vlAccrued = $vlBalance ? (float) $vlBalance->total_accrued      : null;
        $vlUsed    = $vlBalance ? (float) $vlBalance->total_used         : null;
        $slAccrued = $slBalance ? (float) $slBalance->total_accrued      : null;
        $slUsed    = $slBalance ? (float) $slBalance->total_used         : null;

        /* ── Old balance — prior year's closing balance ──
         * reference_year = year - 1 (e.g. opening 2026 card → read 2025 record).
         * Falls back to 0 if no record exists.
         * ── */
       // Priority 1 — read previous year's closing balance from leave_credit_balance
        $prevYearVl = LeaveCreditBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', 1)
            ->where('year', $year - 1)
            ->first();

        $prevYearSl = LeaveCreditBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', 2)
            ->where('year', $year - 1)
            ->first();

        // Priority 2 — PDF-uploaded old balance record (prior office records)
        $oldBalanceRecord = DB::table('old_balance')
            ->where('employee_id', $employeeId)
            ->where('reference_year', $year)
            ->whereNotNull('pdf_file')
            ->first();

        $oldBalanceHasPdf = $oldBalanceRecord && !empty($oldBalanceRecord->pdf_file);

        if ($oldBalanceHasPdf) {
            $oldBalanceVl    = (float) $oldBalanceRecord->old_vl_balance;
            $oldBalanceSl    = (float) $oldBalanceRecord->old_sl_balance;
            $oldBalanceFound = true;
        } elseif ($prevYearVl || $prevYearSl) {
            // Use exact remaining balance — no rounding
            $oldBalanceVl    = $prevYearVl ? (float) $prevYearVl->getRawOriginal('remaining_balance') : 0;
            $oldBalanceSl    = $prevYearSl ? (float) $prevYearSl->getRawOriginal('remaining_balance') : 0;
            $oldBalanceFound = true;
        } else {
            $oldBalanceVl    = 0;
            $oldBalanceSl    = 0;
            $oldBalanceFound = false;
        }

        $oldBalanceHasPdf = $oldBalanceRecord && !empty($oldBalanceRecord->pdf_file);

        /* ── Resolve which column holds the filing date ──
         * Use application_date if the column exists,
         * otherwise fall back to created_at.
         * ── */
        $hasAppDateCol = DB::getSchemaBuilder()->hasColumn('leave_applications', 'application_date');
        $filingDateCol = $hasAppDateCol ? 'application_date' : 'created_at';

        /* ══════════════════════════════════════════════════════════════
         * REGULAR LEAVE APPLICATIONS
         *
         * Fetched for ALL statuses (PENDING, APPROVED, REJECTED, CANCELLED)
         * so the frontend can track which IDs have been seen/excluded.
         * autoImportApproved() on the frontend filters to APPROVED only.
         *
         * Sorted by filing date ASC → correct sheet order.
         * ══════════════════════════════════════════════════════════════ */
        $regularApplications = LeaveApplication::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($year, $filingDateCol) {
                $q->whereYear($filingDateCol, $year)
                  ->orWhereYear('start_date', $year);
            })
            ->orderByRaw("DATE({$filingDateCol}) ASC")
            ->orderBy('created_at', 'ASC')
            ->get()
            ->map(function ($app) use ($filingDateCol) {
                $rawFiling   = $app->{$filingDateCol} ?? $app->created_at;
                $filingDate  = Carbon::parse($rawFiling ?? $app->created_at)->toDateString();
                $filingMonth = (int) Carbon::parse($filingDate)->month;

                // created_at as a plain datetime string for merge-sort below.
                // Falls back to filing date string if created_at is null so
                // the sortBy() call always has a comparable scalar value.
                $createdAt = $app->created_at
                    ? Carbon::parse($app->created_at)->toDateTimeString()
                    : $filingDate;

                return [
                    'leave_id'         => $app->leave_id,
                    'is_half_day'      => false,
                    'half_day_id'      => null,
                    'leave_type'       => $app->leaveType->type_name        ?? '—',
                    'type_code'        => $app->leaveType->type_code         ?? '—',
                    'is_accrual_based' => $app->leaveType->is_accrual_based  ?? 0,
                    'applied_at'       => $filingDate,
                    'month'            => $filingMonth,
                    'application_date' => $app->application_date,
                    // FIX: Force plain YYYY-MM-DD strings to prevent Carbon/Eloquent from
                    // serialising as UTC ISO-8601 (e.g. "2026-05-24T16:00:00.000000Z"),
                    // which caused JS substring(0,10) to land on the previous day for
                    // Asia/Manila (UTC+8) where midnight local = 16:00 previous day UTC.
                    'start_date'       => $app->start_date ? Carbon::parse($app->start_date)->toDateString() : null,
                    'end_date'         => $app->end_date   ? Carbon::parse($app->end_date)->toDateString()   : null,
                    'no_of_days'       => (float) $app->no_of_days,
                    'details_of_leave' => $app->details_of_leave,
                    'reason'           => $app->reason,
                    'is_monetization'  => (bool) $app->is_monetization,
                    'commutation'      => $app->commutation,
                    'status'           => $app->status,
                    'approved_at'      => $app->approved_at,
                    'reject_reason'    => $app->reject_reason,
                    'sort_date'        => $filingDate,
                    'created_at'       => $createdAt,   // ← used for merge-sort
                ];
            });

        /* ══════════════════════════════════════════════════════════════
         * HALF-DAY APPLICATIONS
         *
         * Fetched for ALL statuses for the year so the frontend can
         * recognise previously excluded/tombstoned half-day IDs and
         * won't re-import them after a save+reload cycle.
         *
         * autoImportApproved() on the frontend still filters to
         * status === 'APPROVED' before inserting any rows.
         *
         * Columns used:
         *   half_day_id      → unique ID, prefixed 'hd_' on the frontend
         *   employee_id      → to scope per employee
         *   leave_type_id    → joined to leave_type for type_name / type_code
         *   date_of_absence  → used as both start_date and end_date
         *   time_period      → AM or PM, shown in details_of_leave
         *   application_date → filing date (same day as absence for half-days)
         *   status           → PENDING / APPROVED / REJECTED / CANCELLED
         *   created_at       → used for merge-sort with regular leaves
         *
         * Column mapping on the leave card sheet:
         *   VL half-day → tardy_undertime (0.5) — deducts from VL balance
         *   SL half-day → taken_sl (0.5)         — deducts from SL balance
         *   The frontend autoImportApproved() handles this distinction via type_code.
         * ══════════════════════════════════════════════════════════════ */
        $halfDayApplications = DB::table('half_day as hd')
            ->join('leave_type as lt', 'lt.leave_type_id', '=', 'hd.leave_type_id')
            ->where('hd.employee_id', $employeeId)
            ->whereYear('hd.date_of_absence', $year)  // all statuses — frontend filters
            ->select([
                'hd.half_day_id',
                'hd.employee_id',
                'hd.leave_type_id',
                'hd.date_of_absence',
                'hd.time_period',
                'hd.application_date',
                'hd.status',
                'hd.created_at',                       // ← for merge-sort
                'lt.type_name  as leave_type_name',
                'lt.type_code',
                'lt.is_accrual_based',
            ])
            ->orderBy('hd.date_of_absence', 'ASC')
            ->orderBy('hd.half_day_id', 'ASC')
            ->get()
            ->map(function ($hd) {
                $absenceDate  = Carbon::parse($hd->date_of_absence)->toDateString();
                $absenceMonth = (int) Carbon::parse($hd->date_of_absence)->month;

                // created_at as a plain datetime string for merge-sort below.
                // Falls back to absence date string if created_at is null.
                $createdAt = $hd->created_at
                    ? Carbon::parse($hd->created_at)->toDateTimeString()
                    : $absenceDate;

                return [
                    // 'hd_N' format used as the unique frontend tracking key
                    'leave_id'         => 'hd_' . $hd->half_day_id,
                    'is_half_day'      => true,
                    'half_day_id'      => $hd->half_day_id,
                    'leave_type'       => $hd->leave_type_name,
                    'type_code'        => $hd->type_code,              // VL or SL
                    'is_accrual_based' => (bool) $hd->is_accrual_based,
                    'applied_at'       => $absenceDate,
                    'month'            => $absenceMonth,
                    'application_date' => $hd->application_date,
                    'start_date'       => $absenceDate,
                    'end_date'         => $absenceDate,
                    'no_of_days'       => 0.5,                         // always 0.5
                    'details_of_leave' => $hd->time_period . ' Half Day', // e.g. "AM Half Day"
                    'reason'           => null,
                    'is_monetization'  => false,
                    'commutation'      => null,
                    'status'           => $hd->status,
                    'approved_at'      => null,
                    'reject_reason'    => null,
                    'sort_date'        => $absenceDate,
                    'created_at'       => $createdAt,   // ← used for merge-sort
                ];
            });

        /* ══════════════════════════════════════════════════════════════
         * MERGE — sort by created_at ASC so half-days interleave with
         * regular leaves in the exact order they were inserted into the
         * DB, regardless of type.
         *
         * Previously the two collections were simply concatenated
         * (regular first, half-days appended after), which caused ALL
         * half-days to appear at the bottom of the sheet even when they
         * were created before later regular-leave records.
         * ══════════════════════════════════════════════════════════════ */
        $applications = $regularApplications
            ->concat($halfDayApplications)
            ->sortBy('created_at')
            ->values();

        /* ── Saved leave card header ── */
        $card = LeaveCard::where('user_id', $userId) // FIXED: Query by user_id
                         ->where('year', $year)
                         ->first();

        /* ── Shared old_balance payload ── */
        // FIX: Use the already-resolved $oldBalanceFound bool instead of
        // re-evaluating $oldBalanceRecord (which can be null when no PDF row
        // exists but a prev-year credit-balance record was used).
        $oldBalancePayload = [
            'vl'             => $oldBalanceVl,
            'sl'             => $oldBalanceSl,
            'reference_year' => $year,
            'found'          => $oldBalanceFound || $oldBalanceVl > 0 || $oldBalanceSl > 0,
            'has_pdf'        => $oldBalanceHasPdf,
        ];

        /* ── No saved card yet — return blank scaffold ── */
        if (!$card) {
            return response()->json([
                // FIX: Return success:false so JS knows to call populateEditorBlank(),
                // but include ALL fields so the blank editor renders completely.
                'success'      => false,
                'employee'     => $this->employeePayload($employee),
                'card'         => null,        // explicit null — frontend checks data.card
                'entries'      => [],           // empty array, never undefined
                'current_vl'   => $currentVl,
                'current_sl'   => $currentSl,
                'vl_accrued'   => $vlAccrued,
                'vl_used'      => $vlUsed,
                'sl_accrued'   => $slAccrued,
                'sl_used'      => $slUsed,
                'old_balance'  => $oldBalancePayload,
                'applications' => $applications,
            ]);
        }

        /* ══════════════════════════════════════════════════════════════
         * SAVED ENTRIES
         *
         * Maps every saved leave_card_entry row back to the frontend
         * format. Key points:
         *
         *   leave_application_id → used to populate importedLeaveIds
         *                          for regular leaves on reload
         *
         *   half_day_id          → used to populate importedLeaveIds
         *                          (as 'hd_N') for half-day entries on reload
         *                          REQUIRES the half_day_id column to exist
         *                          (added by migration)
         *
         *   date_particulars === '--- EXCLUDED ---'
         *                      → tombstone row; frontend adds IDs to
         *                        excludedLeaveIds so they are never
         *                        re-imported after deletion
         *
         *   'as per hr' particulars → hr_vl_balance / hr_sl_balance
         *                             returned so frontend restores
         *                             the hard-override on reload
         * ══════════════════════════════════════════════════════════════ */
        $entries = LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                        ->orderBy('entry_order')
                        ->get()
                        ->map(function ($e) {
                            $isSep = str_contains($e->date_particulars ?? '', '--- MONTH SEPARATOR ---');
                            $isHr  = str_contains(strtolower($e->date_particulars ?? ''), 'as per hr');

                            return [
                                'entry_id'             => $e->entry_id,
                                'is_separator'         => $isSep,
                                'entry_order'          => $e->entry_order,
                                'month'                => $e->month,
                                'date_particulars'     => $isSep ? null : $e->date_particulars,
                                'earned_vl'            => $e->earned_vl,
                                'earned_sl'            => $e->earned_sl,
                                'taken_vl'             => $e->taken_vl,
                                'taken_sl'             => $e->taken_sl,
                                'leave_wop'            => $e->leave_wop,
                                'tardy_undertime'      => $e->tardy_undertime,
                                'balance_vl'           => $e->balance_vl,
                                'balance_sl'           => $e->balance_sl,
                                // Restore HR override values on reload for "As per HR" rows
                                'hr_vl_balance'        => $isHr ? $e->balance_vl : null,
                                'hr_sl_balance'        => $isHr ? $e->balance_sl : null,
                                'remarks'              => $e->remarks,
                                'status'               => $e->status,
                                'leave_application_id' => $e->leave_application_id,
                                'half_day_id'          => $e->half_day_id ?? null, // ← from migration column
                                'is_manual'            => $e->is_manual,
                            ];
                        });

        return response()->json([
            'success'      => true,
            'employee'     => $this->employeePayload($employee),
            'card'         => [
                'leave_card_id' => $card->leave_card_id,
                'opening_vl'    => $card->opening_vl,
                'opening_sl'    => $card->opening_sl,
            ],
            'entries'      => $entries,
            'current_vl'   => $currentVl,
            'current_sl'   => $currentSl,
            'vl_accrued'   => $vlAccrued,
            'vl_used'      => $vlUsed,
            'sl_accrued'   => $slAccrued,
            'sl_used'      => $slUsed,
            'old_balance'  => $oldBalancePayload,
            'applications' => $applications,
        ]);
    }

    /* ═══════════════════════════════════════════════
     * SAVE — upsert card + entries
     *
     * Tombstone rows (date_particulars = '--- EXCLUDED ---') are saved
     * with leave_application_id OR half_day_id set so they can be
     * recognised on reload and re-populate excludedLeaveIds correctly.
     *
     * After saving entries, recalculates running VL/SL balances
     * identically to the frontend recalcAll() function and syncs
     * the leave_credit_balance table.
     * ═══════════════════════════════════════════════ */
    public function save(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'year'        => 'required|integer|min:2000|max:2099',
            'opening_vl'  => 'nullable|numeric',
            'opening_sl'  => 'nullable|numeric',
            'entries'     => 'nullable|array',
        ]);

        // FIX: Look up by employee_id, not PK (user_id)
        $employee = Employee::where('employee_id', $request->employee_id)->firstOrFail();
        $userId = $employee->user_id;

        // FIX: If the frontend sends opening_vl/opening_sl as 0 (new/blank card),
        // resolve them from the prior year's closing balance — exactly the same
        // priority chain that show() uses to build the $oldBalancePayload.
        // This ensures the 2026 card opens with 228.414/303.742 instead of 0/0
        // when the 2025 card's leave_credit_balance row now exists after the FK fix.
        $openingVlRaw = $request->opening_vl;
        $openingSlRaw = $request->opening_sl;

        if (($openingVlRaw === null || (float)$openingVlRaw === 0.0) &&
            ($openingSlRaw === null || (float)$openingSlRaw === 0.0)) {

            // Priority 1 — prior year's closing balance from leave_credit_balance
            $prevVlRow = DB::table('leave_credit_balance')
                ->where('employee_id', $request->employee_id)
                ->where('leave_type_id', 1)
                ->where('year', $request->year - 1)
                ->first();
            $prevSlRow = DB::table('leave_credit_balance')
                ->where('employee_id', $request->employee_id)
                ->where('leave_type_id', 2)
                ->where('year', $request->year - 1)
                ->first();

            if ($prevVlRow || $prevSlRow) {
                $openingVlRaw = $prevVlRow ? $prevVlRow->remaining_balance : 0;
                $openingSlRaw = $prevSlRow ? $prevSlRow->remaining_balance : 0;
            } else {
                // Priority 2 — old_balance PDF record
                $oldBal = DB::table('old_balance')
                    ->where('employee_id', $request->employee_id)
                    ->where('reference_year', $request->year)
                    ->whereNotNull('pdf_file')
                    ->first();
                if ($oldBal) {
                    $openingVlRaw = $oldBal->old_vl_balance;
                    $openingSlRaw = $oldBal->old_sl_balance;
                }
            }
        }

        DB::beginTransaction();
        try {
            /* ── Upsert the card header ── */
            $card = LeaveCard::updateOrCreate(
                [
                    'user_id' => $userId,
                    'year'    => $request->year,
                ],
                [
                    'employee_id' => $request->employee_id,
                    'opening_vl'  => $openingVlRaw ?? 0,
                    'opening_sl'  => $openingSlRaw ?? 0,
                    'created_by'  => Auth::id(),
                ]
            );

            /* ── Delete all existing entries then re-insert ── */
            LeaveCardEntry::where('leave_card_id', $card->leave_card_id)->delete();

            foreach (($request->entries ?? []) as $entry) {
                $isSep = (bool) ($entry['is_separator'] ?? false);

                LeaveCardEntry::create([
                    'leave_card_id'    => $card->leave_card_id,
                    'entry_order'      => $entry['entry_order'] ?? 0,
                    'month'            => isset($entry['month']) && $entry['month']
                                            ? (int) $entry['month'] : null,

                    // Separators get a sentinel string; regular rows get the typed value
                    'date_particulars' => $isSep
                                            ? '--- MONTH SEPARATOR ---'
                                            : ($entry['date_particulars'] ?? null),

                    'earned_vl'        => $isSep ? null : ($entry['earned_vl']       ?? null),
                    'earned_sl'        => $isSep ? null : ($entry['earned_sl']       ?? null),
                    'taken_vl'         => $isSep ? null : ($entry['taken_vl']        ?? null),
                    'taken_sl'         => $isSep ? null : ($entry['taken_sl']        ?? null),
                    'leave_wop'        => $isSep ? null : ($entry['leave_wop']       ?? null),
                    'tardy_undertime'  => $isSep ? null : ($entry['tardy_undertime'] ?? null),

                    // Strip the '—' placeholder the frontend uses for empty balance cells
                    'balance_vl'       => $isSep ? null : (
                        isset($entry['balance_vl'])
                        && $entry['balance_vl'] !== ''
                        && $entry['balance_vl'] !== '—'
                            ? $entry['balance_vl'] : null
                    ),
                    'balance_sl'       => $isSep ? null : (
                        isset($entry['balance_sl'])
                        && $entry['balance_sl'] !== ''
                        && $entry['balance_sl'] !== '—'
                            ? $entry['balance_sl'] : null
                    ),

                    'remarks'              => $isSep ? null : ($entry['remarks'] ?? null),
                    'status'               => $isSep ? null : ($entry['status']  ?? null),

                    // Regular leave FK — null for half-day or manual rows
                    'leave_application_id' => $isSep ? null : ($entry['leave_application_id'] ?? null),

                    // Half-day FK — persists tombstones so deleted half-days
                    // are never re-imported after a save + reload cycle.
                    // Requires the half_day_id column added by migration.
                    'half_day_id'          => $isSep ? null : ($entry['half_day_id'] ?? null),

                    'is_manual'            => (int) ($entry['is_manual'] ?? 1),
                    'created_by'           => Auth::id(),
                ]);
            }

            /* ══════════════════════════════════════════════════════════
             * RECALCULATE & SYNC LEAVE CREDIT BALANCES
             *
             * Mirrors the frontend recalcAll() formula exactly:
             *
             *   VL running = opening_vl
             *              + earned_vl
             *              - taken_vl
             *              - tardy_undertime   ← half-day VL lands here
             *              - leave_wop
             *
             *   SL running = opening_sl
             *              + earned_sl
             *              - taken_sl          ← half-day SL lands here
             *
             * "As per HR" rows hard-override the running balance,
             * identical to the frontend behaviour.
             * ══════════════════════════════════════════════════════════ */
            // Use the resolved opening values (auto-populated from prior year if request sent 0)
            $openingVl = (float) ($openingVlRaw ?? 0);
            $openingSl = (float) ($openingSlRaw ?? 0);

            $runningVl     = $openingVl;
            $runningSl     = $openingSl;
            $totalEarnedVl = 0;
            $totalEarnedSl = 0;
            $totalTakenVl  = 0;
            $totalTakenSl  = 0;
            $totalWop      = 0;
            $totalTardy    = 0;

            // Walk in entry_order — same sequence as the sheet
            $sortedEntries = collect($request->entries ?? [])
                ->sortBy('entry_order')
                ->values();

            foreach ($sortedEntries as $entry) {
                // Separators and tombstones carry no numeric values
                if ($entry['is_separator'] ?? false) continue;
                if (($entry['date_particulars'] ?? '') === '--- EXCLUDED ---') continue;

                $earnedVl = (float) ($entry['earned_vl']       ?? 0);
                $earnedSl = (float) ($entry['earned_sl']       ?? 0);
                $takenVl  = (float) ($entry['taken_vl']        ?? 0);
                $takenSl  = (float) ($entry['taken_sl']        ?? 0);
                $wop      = (float) ($entry['leave_wop']       ?? 0);
                $tardy    = (float) ($entry['tardy_undertime'] ?? 0);

                $runningVl += $earnedVl - $takenVl - $tardy - $wop;
                $runningSl += $earnedSl - $takenSl;

                $totalEarnedVl += $earnedVl;
                $totalEarnedSl += $earnedSl;
                $totalTakenVl  += $takenVl;
                $totalTakenSl  += $takenSl;
                $totalWop      += $wop;
                $totalTardy    += $tardy;

                // "As per HR" hard override — mirrors frontend recalcAll()
                $particulars = strtolower($entry['date_particulars'] ?? '');
                if (str_contains($particulars, 'as per hr')) {
                    $hrVl = isset($entry['balance_vl'])
                            && $entry['balance_vl'] !== ''
                            && $entry['balance_vl'] !== '—'
                        ? (float) $entry['balance_vl'] : null;

                    $hrSl = isset($entry['balance_sl'])
                            && $entry['balance_sl'] !== ''
                            && $entry['balance_sl'] !== '—'
                        ? (float) $entry['balance_sl'] : null;

                    if ($hrVl !== null) $runningVl = $hrVl;
                    if ($hrSl !== null) $runningSl = $hrSl;
                }
            }

            $finalVl     = $runningVl;
            $finalSl     = $runningSl;
            $totalUsedVl = $totalTakenVl + $totalTardy + $totalWop;
            $totalUsedSl = $totalTakenSl;

            // FIX: `leave_credit_balance` has FK `fk_lcb_user` → employee.user_id (auto-increment PK).
            // Must use $userId (e.g. 2), NOT $request->employee_id (e.g. 160624).
            // Passing employee_id into the user_id column caused SQLSTATE[23000] FK violation
            // and the entire save was rolled back, leaving no balance row for this year —
            // which is also why the next year's opening balance showed 0.000.
            DB::table('leave_credit_balance')->updateOrInsert(
                [
                    'user_id'       => $userId,               // FK → employee.user_id (e.g. 2)
                    'employee_id'   => $request->employee_id, // payroll/govt ID (e.g. 160624)
                    'leave_type_id' => 1,
                    'year'          => $request->year,
                ],
                [
                    'total_accrued'     => round($openingVl + $totalEarnedVl, 3),
                    'total_used'        => round($totalUsedVl, 3),
                    'remaining_balance' => $finalVl,
                    'updated_at'        => now(),
                ]
            );

            // Sync SL balance
            DB::table('leave_credit_balance')->updateOrInsert(
                [
                    'user_id'       => $userId,               // FK → employee.user_id (e.g. 2)
                    'employee_id'   => $request->employee_id, // payroll/govt ID (e.g. 160624)
                    'leave_type_id' => 2,
                    'year'          => $request->year,
                ],
                [
                    'total_accrued'     => round($openingSl + $totalEarnedSl, 3),
                    'total_used'        => round($totalUsedSl, 3),
                    'remaining_balance' => $finalSl,
                    'updated_at'        => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'success'       => true,
                'leave_card_id' => $card->leave_card_id,
                'opening_vl'    => round($openingVl, 3),   // resolved value (may differ from request if auto-populated)
                'opening_sl'    => round($openingSl, 3),   // resolved value
                'current_vl'    => round($finalVl, 3),
                'current_sl'    => round($finalSl, 3),
                'vl_accrued'    => round($openingVl + $totalEarnedVl, 3),
                'sl_accrued'    => round($openingSl + $totalEarnedSl, 3),
                'vl_used'       => round($totalUsedVl, 3),
                'sl_used'       => round($totalUsedSl, 3),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /* ═══════════════════════════════════════════════
     * PRINT — single employee leave card print view
     * ═══════════════════════════════════════════════ */
    public function print(int $employeeId, int $year)
    {
        // FIX: Look up by employee_id, not PK (user_id)
        $employee = Employee::with(['position', 'department'])
                            ->where('employee_id', $employeeId)
                            ->firstOrFail();

        $card = LeaveCard::where('user_id', $employee->user_id) // FIXED: Query by user_id
                         ->where('year', $year)
                         ->first();

        $entries = $card
            ? LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                            ->orderBy('entry_order')
                            ->get()
            : collect();

        $applications = LeaveApplication::with('leaveType')
            ->where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->get();

        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 1)
                        ->where('year', $year)
                        ->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 2)
                        ->where('year', $year)
                        ->first();

        return view('leave_card.print', compact(
            'employee', 'card', 'entries', 'applications', 'year', 'vlBalance', 'slBalance'
        ));
    }

    /* ═══════════════════════════════════════════════
     * PRINT ALL — all active employees
     * ═══════════════════════════════════════════════ */
    public function printAll()
    {
        $year      = now()->year;
        $employees = Employee::with(['position', 'department'])
                             ->where('is_active', 1)
                             ->orderBy('last_name')
                             ->get();

        $allData = $employees->map(function ($employee) use ($year) {
            $card = LeaveCard::where('user_id', $employee->user_id) // FIXED: Query by user_id
                             ->where('year', $year)
                             ->first();

            $entries = $card
                ? LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                                ->orderBy('entry_order')
                                ->get()
                : collect();

            $applications = LeaveApplication::with('leaveType')
                ->where('employee_id', $employee->employee_id)
                ->whereYear('start_date', $year)
                ->orderBy('start_date')
                ->get();

            $vlBalance = LeaveCreditBalance::where('employee_id', $employee->employee_id)
                            ->where('leave_type_id', 1)
                            ->where('year', $year)
                            ->first();

            $slBalance = LeaveCreditBalance::where('employee_id', $employee->employee_id)
                            ->where('leave_type_id', 2)
                            ->where('year', $year)
                            ->first();

            return compact('employee', 'card', 'entries', 'applications', 'vlBalance', 'slBalance');
        });

        return view('leave_card.print-all', compact('allData', 'year'));
    }

    public function oldBalancePdf(int $employeeId, int $year)
    {
        $record = DB::table('old_balance')
            ->where('employee_id', $employeeId)
            ->where('reference_year', $year)
            ->first();

        if (!$record || !$record->pdf_file) {
            abort(404, 'No PDF found for this employee and year.');
        }

        return response($record->pdf_file, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="old_balance_' . ($year - 1) . '_emp' . $employeeId . '.pdf"',
        ]);
    }

    /* ═══════════════════════════════════════════════
     * PRIVATE HELPER — employee payload shape
     * ═══════════════════════════════════════════════ */
    private function employeePayload(Employee $emp): array
    {
        return [
            'employee_id'           => $emp->employee_id,
            'user_id'               => $emp->user_id, // Added user_id
            'last_name'             => $emp->last_name,
            'first_name'            => $emp->first_name,
            'middle_name'           => $emp->middle_name,
            'extension_name'        => $emp->extension_name,
            'position_name'         => $emp->position->position_name    ?? '—',
            'department_name'       => $emp->department->department_name ?? '—',
            'salary'                => $emp->salary,
            'formatted_employee_id' => $emp->formatted_employee_id ?? $emp->employee_id,
        ];
    }
}