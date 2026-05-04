<?php
// app/Http/Controllers/LeaveApplicationController.php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HalfDay;
use App\Models\LeaveApplication;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\LeaveDetailGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveApplicationController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $employeeId = session('employee_id');

        $employee = Employee::with(['position', 'department'])
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        $year = now()->year;

        $vlType    = LeaveType::where('type_code', 'VL')->first();
        $vlBalance = null;
        if ($vlType) {
            $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                ->where('leave_type_id', $vlType->leave_type_id)
                ->where('year', $year)
                ->first();
            if (!$vlBalance) {
                $vlBalance = LeaveCreditBalance::create([
                    'employee_id'       => $employeeId,
                    'leave_type_id'     => $vlType->leave_type_id,
                    'year'              => $year,
                    'total_accrued'     => 0,
                    'total_used'        => 0,
                    'remaining_balance' => 0,
                ]);
            }
        }

        $slType    = LeaveType::where('type_code', 'SL')->first();
        $slBalance = null;
        if ($slType) {
            $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                ->where('leave_type_id', $slType->leave_type_id)
                ->where('year', $year)
                ->first();
            if (!$slBalance) {
                $slBalance = LeaveCreditBalance::create([
                    'employee_id'       => $employeeId,
                    'leave_type_id'     => $slType->leave_type_id,
                    'year'              => $year,
                    'total_accrued'     => 0,
                    'total_used'        => 0,
                    'remaining_balance' => 0,
                ]);
            }
        }

        $allBalances  = LeaveCreditBalance::where('employee_id', $employeeId)->where('year', $year)->get();
        $totalBalance = $allBalances->sum('remaining_balance');
        $accrualRate  = '+1.25';

        $creditBalancesJson = LeaveCreditBalance::where('employee_id', $employeeId)
            ->where('year', $year)->get()
            ->keyBy('leave_type_id')
            ->map(fn($b) => [
                'remaining_balance' => (float) $b->remaining_balance,
                'total_accrued'     => (float) $b->total_accrued,
                'total_used'        => (float) $b->total_used,
            ])->toArray();

        $dailyRate = $employee->salary ? round($employee->salary / 22, 2) : 0;

        $leaveApps = LeaveApplication::with(['leaveType', 'approvedBy'])
            ->where('employee_id', $employeeId)
            ->where('is_monetization', 0)
            ->orderByDesc('application_date')->get();

        $monetizationApps = LeaveApplication::with(['leaveType', 'approvedBy'])
            ->where('employee_id', $employeeId)
            ->where('is_monetization', 1)
            ->orderByDesc('application_date')->get();

        $leaveTypes = LeaveType::where('is_active', 1)->orderBy('type_name')->get();

        // ── Conflict data for the blade calendar ──────────────────────────
        $halfDayDates = HalfDay::where('employee_id', $employeeId)
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->pluck('date_of_absence')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        // ── Max-days enforcement data ─────────────────────────────────────
        $usedDaysThisYear = LeaveApplication::where('employee_id', $employeeId)
            ->where('is_monetization', 0)
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->whereYear('application_date', $year)
            ->groupBy('leave_type_id')
            ->selectRaw('leave_type_id, SUM(no_of_days) as total_used')
            ->pluck('total_used', 'leave_type_id')
            ->toArray();

        $maxDaysJson = LeaveType::where('is_active', 1)
            ->whereNotNull('max_days')
            ->get()
            ->keyBy('leave_type_id')
            ->map(fn($lt) => [
                'max_days'  => (float) $lt->max_days,
                'used_days' => (float) ($usedDaysThisYear[$lt->leave_type_id] ?? 0),
                'remaining' => max(0, (float) $lt->max_days - (float) ($usedDaysThisYear[$lt->leave_type_id] ?? 0)),
                'type_name' => $lt->type_name,
            ])
            ->toArray();

        // ── Signatories ───────────────────────────────────────────────────
        $signatories = DB::table('signatory_options')->orderBy('sort_order')->get();

        return view('application.leave', compact(
            'employee', 'vlBalance', 'slBalance', 'totalBalance',
            'accrualRate', 'creditBalancesJson', 'dailyRate',
            'leaveApps', 'monetizationApps', 'leaveTypes',
            'halfDayDates',
            'maxDaysJson',
            'signatories',
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|integer|exists:leave_type,leave_type_id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        $employeeId = session('employee_id');
        $year       = now()->year;
        $leaveType  = LeaveType::findOrFail($request->leave_type_id);

        // ── Count working days from the actual selected dates ─────────────
        $selectedDates = $request->input('leave_dates', []);
        $selectedDates = array_values(array_unique(array_filter($selectedDates)));
        sort($selectedDates);
        $days = count($selectedDates);

        if ($days === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No leave dates were selected.',
            ], 422);
        }

        $start = new \DateTime($selectedDates[0]);
        $end   = new \DateTime($selectedDates[count($selectedDates) - 1]);

        // ── Conflict: date range must not overlap an existing active leave ──
        $overlapConflict = LeaveApplication::where('employee_id', $employeeId)
            ->where('is_monetization', 0)
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<=', $request->end_date)
            ->where('end_date',   '>=', $request->start_date)
            ->exists();

        if ($overlapConflict) {
            return response()->json([
                'success' => false,
                'message' => 'The selected date range overlaps with an existing leave application.',
            ], 422);
        }

        // ── Conflict: no selected date may have an active half-day record ──
        $requestedDates = $selectedDates;

        $halfDayConflict = HalfDay::where('employee_id', $employeeId)
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->whereIn(DB::raw('DATE(date_of_absence)'), $requestedDates)
            ->exists();

        if ($halfDayConflict) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected dates already have a half-day certification. Please cancel the half-day first.',
            ], 422);
        }

        // ── Max-days per year enforcement ─────────────────────────────────
        if ($leaveType->max_days !== null) {
            $usedThisYear = LeaveApplication::where('employee_id', $employeeId)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->where('is_monetization', 0)
                ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
                ->whereYear('application_date', $year)
                ->sum('no_of_days');

            $remaining = max(0, $leaveType->max_days - $usedThisYear);

            if (($usedThisYear + $days) > $leaveType->max_days) {
                return response()->json([
                    'success' => false,
                    'message' => "Exceeded the {$leaveType->max_days}-day annual limit for {$leaveType->type_name}. "
                               . "You have used {$usedThisYear} day(s) this year — only {$remaining} day(s) remaining.",
                ], 422);
            }
        }

        // ── Balance check ─────────────────────────────────────────────────
        $creditBalance = LeaveCreditBalance::firstOrCreate(
            ['employee_id' => $employeeId, 'leave_type_id' => $leaveType->leave_type_id, 'year' => $year],
            ['total_accrued' => 0, 'total_used' => 0, 'remaining_balance' => 0]
        );

        if ($leaveType->is_accrual_based && $creditBalance->remaining_balance < $days) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient balance. You have {$creditBalance->remaining_balance} days available but requested {$days} days.",
            ], 422);
        }

        $application = LeaveApplication::create([
            'employee_id'       => $employeeId,
            'leave_type_id'     => $leaveType->leave_type_id,
            'credit_balance_id' => $creditBalance->credit_balance_id,
            'details_of_leave'  => $request->details_of_leave ?? null,
            'application_date'  => now()->toDateString(),
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'no_of_days'        => $days,
            'leave_dates'       => json_encode($selectedDates),
            'reason'            => $request->reason ?? null,
            'commutation'       => $request->commutation ?? 'NOT_REQUESTED',
            'status'            => 'PENDING',
            'is_monetization'   => 0,
        ]);

        try {
            $application->load(['employee', 'leaveType']);
            Notification::notifyNewLeave($application);
        } catch (\Exception $e) {}

        return response()->json(['success' => true, 'message' => 'Leave application submitted successfully.', 'leave_id' => $application->leave_id]);
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE MONETIZATION
    // ─────────────────────────────────────────────────────────────
    public function storeMonetization(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|integer|exists:leave_type,leave_type_id',
            'no_of_days'    => 'required|numeric|min:1|max:30',
        ]);

        $employeeId    = session('employee_id');
        $year          = now()->year;
        $leaveType     = LeaveType::findOrFail($request->leave_type_id);

        if (!$leaveType->is_accrual_based) {
            return response()->json(['success' => false, 'message' => 'Only accrual-based leave types can be monetized.'], 422);
        }

        $creditBalance = LeaveCreditBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveType->leave_type_id)->where('year', $year)->first();

        if (!$creditBalance || $creditBalance->remaining_balance < $request->no_of_days) {
            return response()->json(['success' => false, 'message' => 'Insufficient leave balance for monetization.'], 422);
        }

        $application = LeaveApplication::create([
            'employee_id'       => $employeeId,
            'leave_type_id'     => $leaveType->leave_type_id,
            'credit_balance_id' => $creditBalance->credit_balance_id,
            'application_date'  => now()->toDateString(),
            'start_date'        => now()->toDateString(),
            'end_date'          => now()->toDateString(),
            'no_of_days'        => $request->no_of_days,
            'reason'            => $request->reason ?? null,
            'commutation'       => 'NOT_REQUESTED',
            'status'            => 'PENDING',
            'is_monetization'   => 1,
        ]);

        try {
            $application->load(['employee', 'leaveType']);
            Notification::notifyNewLeave($application);
        } catch (\Exception $e) {}

        return response()->json(['success' => true, 'message' => 'Monetization request submitted successfully.']);
    }

    // ─────────────────────────────────────────────────────────────
    //  CANCEL
    // ─────────────────────────────────────────────────────────────
    public function cancel($id)
    {
        $employeeId  = session('employee_id');
        $application = LeaveApplication::where('leave_id', $id)->where('employee_id', $employeeId)->firstOrFail();

        if ($application->status !== 'PENDING') {
            return response()->json(['success' => false, 'message' => 'Only PENDING applications can be cancelled.'], 422);
        }

        $application->update(['status' => 'CANCELLED']);
        return response()->json(['success' => true, 'message' => 'Application cancelled successfully.']);
    }

    // ─────────────────────────────────────────────────────────────
    //  PDF
    // ─────────────────────────────────────────────────────────────
    public function pdf($id)
    {
        $employeeId = session('employee_id');

        $app = LeaveApplication::with([
                'employee.position',
                'employee.department',
                'leaveType',
            ])
            ->where('leave_id', $id)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

            // ── Decode stored selected dates ──────────────────────────────────
        $leaveDates = collect();
        if (!empty($app->leave_dates)) {
            $raw = is_array($app->leave_dates) ? $app->leave_dates : json_decode($app->leave_dates, true);
            $leaveDates = collect($raw ?? [])
                ->map(fn($d) => \Carbon\Carbon::parse($d))
                ->sortBy(fn($d) => $d->timestamp)
                ->values();
        }

        $year = $app->start_date ? $app->start_date->year : now()->year;

        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
            ->whereHas('leaveType', fn($q) => $q->where('type_code', 'VL'))
            ->where('year', $year)->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
            ->whereHas('leaveType', fn($q) => $q->where('type_code', 'SL'))
            ->where('year', $year)->first();

        $allLeaveTypes = LeaveType::where('is_active', 1)
            ->orderBy('leave_type_id')
            ->get();

        $detailGroups = LeaveDetailGroup::with(['items' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $commutationOptions = DB::table('commutation_options')
            ->orderBy('sort_order')->get();

        $recommendationOptions = DB::table('recommendation_options')
            ->orderBy('sort_order')->get();

        return view('application.leave-pdf', compact(
            'app',
            'vlBalance',
            'slBalance',
            'allLeaveTypes',
            'detailGroups',
            'commutationOptions',
            'recommendationOptions',
            'leaveDates',
        ));
    }
}