<?php
// app/Http/Controllers/Admin/AdminHalfDayController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HalfDay;
use App\Models\LeaveCreditBalance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminHalfDayController extends Controller
{
    /* ══════════════════════════════════════════════════════
       PRIVATE HELPER — resolves the current Provincial
       Agriculturist dynamically (position_id = 1, active).
    ══════════════════════════════════════════════════════ */
    private function getOfficeHeadName(): string
    {
        $head = Employee::where('position_id', 1)
                        ->where('is_active', 1)
                        ->first();

        if (!$head) {
            return config('app.office_head', 'ALMIRANTE A. ABAD');
        }

        $firstName  = strtoupper(trim($head->first_name ?? ''));
        $middleInit = $head->middle_name
            ? strtoupper($head->middle_name[0]) . '.'
            : '';
        $lastName   = strtoupper(trim($head->last_name ?? ''));

        return trim("{$firstName} {$middleInit} {$lastName}");
    }

    /* ══════════════════════════════════════════════════════
       INDEX
       GET /admin/halfday
    ══════════════════════════════════════════════════════ */
    public function index()
    {
        $halfDays = HalfDay::with([
                'employee.department',
                'employee.position',
                'leaveType',
                'approvedBy',
            ])
            ->orderByRaw("FIELD(status,'PENDING','APPROVED','REJECTED','CANCELLED')")
            ->orderBy('application_date', 'desc')
            ->get();

        $officeHead = $this->getOfficeHeadName();

        return view('admin.leave.halfday', compact('halfDays', 'officeHead'));
    }

    /* ══════════════════════════════════════════════════════
       STATUS UPDATE
       POST /admin/halfday/{id}/status

       NOTE ON BALANCE DEDUCTION:
       The DB trigger `trg_half_day_approved` (AFTER UPDATE on half_day)
       handles the 0.5-day deduction automatically when status changes
       to APPROVED. Do NOT deduct manually here — doing so causes a
       double deduction (controller 0.5 + trigger 0.5 = 1.0 full day).

       The trigger `trg_half_day_cancelled` handles restoring the balance
       when status changes to CANCELLED or REJECTED.
    ══════════════════════════════════════════════════════ */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED,CANCELLED',
            'reason' => 'nullable|string|max:1000',
        ]);

        $hd        = HalfDay::with('employee')->findOrFail($id);
        $newStatus = $request->input('status');
        $reason    = $request->input('reason');

        // ── Guard 1: employee must exist ── (UPDATED: Check user_id)
        if (!$hd->user_id || !$hd->employee) {
            return response()->json([
                'success' => false,
                'message' => 'This record has no linked employee. Please fix the data before changing status.',
            ], 422);
        }

        // ── Guard 2: leave_type must exist ────────────────────────────────────
        if (!$hd->leave_type_id) {
            return response()->json([
                'success' => false,
                'message' => 'This record has no linked leave type. Please fix the data before changing status.',
            ], 422);
        }

        // ── Guard 3: prevent re-processing finalised records ──────────────────
        if (in_array($hd->status, ['APPROVED', 'REJECTED', 'CANCELLED']) && $hd->status !== $newStatus) {
            return response()->json([
                'success' => false,
                'message' => "Cannot change status from {$hd->status}.",
            ], 422);
        }

        // ── Guard 4: check sufficient balance before approving ────────────────
        // We only check here — the actual deduction is done by the DB trigger.
        if ($newStatus === 'APPROVED') {
            $credit = LeaveCreditBalance::where('user_id',   $hd->user_id) // ── UPDATED ──
                                        ->where('leave_type_id', $hd->leave_type_id)
                                        ->first();

            if (!$credit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No leave credit balance record found for this employee and leave type.',
                ], 422);
            }

            if ((float) $credit->remaining_balance < 0.5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient leave credit balance — at least 0.5 days required.',
                ], 422);
            }
        }

        try {
            // ── Always stamp approved_date with today regardless of status.
            //    The DB trigger fires after this UPDATE and handles the balance
            //    deduction (APPROVED) or restoration (CANCELLED / REJECTED).
            $updates = [
                'status'        => $newStatus,
                'updated_at'    => Carbon::now(),
                'approved_by'   => auth()->user()->employee_id ?? auth()->id(), // Using Legacy Employee ID for approved_by tracking
                'approved_date' => Carbon::now()->toDateString(),
            ];

            if ($newStatus === 'REJECTED') {
                $updates['reason'] = $reason;
            }

            DB::table('half_day')
                ->where('half_day_id', $hd->half_day_id)
                ->update($updates);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        // Reload the record with the approvedBy relationship so we can
        // return the approver's formatted name to the frontend.
        $hd->refresh()->load('approvedBy');

        $approvedByName = null;
        if ($hd->approvedBy) {
            $approvedByName = trim(
                ($hd->approvedBy->last_name  ?? '') . ', ' .
                ($hd->approvedBy->first_name ?? '')
            );
        }

        return response()->json([
            'success'       => true,
            'approved_date' => $hd->approved_date
                ? Carbon::parse($hd->approved_date)->format('M d, Y')
                : null,
            'approved_by'   => $approvedByName,
        ]);
    }

    /* ══════════════════════════════════════════════════════
       CERTIFICATION VIEW
       GET /admin/halfday/{id}/cert
    ══════════════════════════════════════════════════════ */
    public function cert($id)
    {
        $halfDay = HalfDay::with([
                'employee.department',
                'employee.position',
                'leaveType',
            ])
            ->findOrFail($id);

        if (!$halfDay->employee) {
            abort(404, 'Employee record not found for this half-day certification.');
        }

        $employee   = $halfDay->employee;
        $officeHead = $this->getOfficeHeadName();

        return view('application.halfday-pdf', compact('halfDay', 'employee', 'officeHead'));
    }
}