<?php
// app/Http/Controllers/Admin/AdminLeaveController.php
// Balance is handled entirely by DB triggers:
//   trg_leave_application_insert   → deducts on submission
//   trg_leave_application_cancelled → restores on CANCELLED or REJECTED
// Do NOT touch leave_credit_balance manually here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\LeaveDetailGroup;          // ← ADD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;        // ← ADD

class AdminLeaveController extends Controller
{
    // ─────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = LeaveApplication::with(['employee.position', 'employee.department', 'leaveType'])
            ->where('is_monetization', 0)
            ->orderByDesc('application_date');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('month'))  $query->whereMonth('application_date', $request->month);
        if ($request->filled('year'))   $query->whereYear('application_date', $request->year);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('employee', fn($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name',  'like', "%$s%")
                  ->orWhere('employee_id','like', "%$s%")
            );
        }

        $leaveApps       = $query->get();
        $leaveTypes      = LeaveType::where('is_active', 1)->get();
        $pendingCount    = LeaveApplication::where('status', 'PENDING')->where('is_monetization', 0)->count();
        $monetizePending = LeaveApplication::where('status', 'PENDING')->where('is_monetization', 1)->count();

        $monetizationApps = LeaveApplication::with(['employee.position', 'employee.department', 'leaveType'])
            ->where('is_monetization', 1)
            ->orderByDesc('application_date')
            ->get();

        return view('admin.leave.index', compact(
            'leaveApps', 'monetizationApps', 'leaveTypes',
            'pendingCount', 'monetizePending'
        ));
    }

    // ─────────────────────────────────────────
    //  APPROVE
    //  Balance was already deducted on INSERT by trigger.
    //  No balance change needed here.
    // ─────────────────────────────────────────
    public function approve(Request $request, $id)
    {
        $app = LeaveApplication::with(['leaveType', 'employee'])->findOrFail($id);

        if ($app->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Application is no longer pending.',
            ], 422);
        }

        // Just update status — trigger handles balance
        $app->update([
            'status'      => 'APPROVED',
            'approved_by' => session('employee_id'),
            'approved_at' => now(),
        ]);

        try {
            Notification::notifyLeaveStatusChange($app, 'APPROVED', session('employee_id'));
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => "Leave approved for {$app->employee->first_name} {$app->employee->last_name}.",
        ]);
    }

    // ─────────────────────────────────────────
    //  REJECT
    //  DB trigger restores balance automatically on REJECTED.
    // ─────────────────────────────────────────
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $app = LeaveApplication::with(['employee', 'leaveType'])->findOrFail($id);

        if ($app->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Application is no longer pending.',
            ], 422);
        }

        // Trigger restores balance automatically
        $app->update([
            'status'        => 'REJECTED',
            'reject_reason' => $request->reason ?? 'Disapproved by administrator.',
            'approved_by'   => session('employee_id'),
            'approved_at'   => now(),
        ]);

        try {
            Notification::notifyLeaveStatusChange($app, 'REJECTED', session('employee_id'));
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Leave application rejected.',
        ]);
    }

    // ─────────────────────────────────────────
    //  UPDATE STATUS (inline badge click)
    //  Trigger handles all balance changes.
    // ─────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,RECEIVED,ON-PROCESS,APPROVED,REJECTED',
            'reason' => 'nullable|string|max:500',
        ]);

        $app       = LeaveApplication::with(['leaveType', 'employee'])->findOrFail($id);
        $oldStatus = $app->status;
        $newStatus = $request->status;

        if ($oldStatus === 'CANCELLED') {
            return response()->json([
                'success' => false,
                'message' => 'Cancelled applications cannot be changed.',
            ], 422);
        }

        if ($oldStatus === $newStatus) {
            return response()->json(['success' => true, 'message' => 'Status unchanged.', 'status' => $newStatus]);
        }

        $updateData = [
            'status'      => $newStatus,
            'approved_by' => session('employee_id'),
            'approved_at' => now(),
        ];

        if ($newStatus === 'REJECTED') {
            $updateData['reject_reason'] = $request->filled('reason')
                ? $request->reason
                : 'Disapproved by administrator.';
        }

        if ($oldStatus === 'REJECTED' && $newStatus !== 'REJECTED') {
            $updateData['reject_reason'] = null;
        }

        // DB trigger handles balance restoration for REJECTED/CANCELLED
        // DB trigger handles nothing for APPROVED (already deducted on INSERT)
        $app->update($updateData);

        try {
            Notification::notifyLeaveStatusChange($app, $newStatus, session('employee_id'));
        } catch (\Exception $e) {}

        $labels = [
            'PENDING'    => 'Pending',
            'RECEIVED'   => 'Received',
            'ON-PROCESS' => 'On-Process',
            'APPROVED'   => 'Approved',
            'REJECTED'   => 'Rejected',
        ];

        return response()->json([
            'success' => true,
            'message' => "Status updated to {$labels[$newStatus]}.",
            'status'  => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────
    //  PDF
    // ─────────────────────────────────────────
    public function pdf($id)
    {
        $app = LeaveApplication::with(['employee.position', 'employee.department', 'leaveType'])
            ->findOrFail($id);

        $year = $app->start_date ? $app->start_date->year : now()->year;

        $vlBalance = LeaveCreditBalance::where('employee_id', $app->employee_id)
            ->whereHas('leaveType', fn($q) => $q->where('type_code', 'VL'))
            ->where('year', $year)->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $app->employee_id)
            ->whereHas('leaveType', fn($q) => $q->where('type_code', 'SL'))
            ->where('year', $year)->first();

        // ── Pull all dynamic content for the PDF ──────────────────
        $allLeaveTypes = LeaveType::where('is_active', 1)
            ->orderBy('leave_type_id')
            ->get();

        $detailGroups = LeaveDetailGroup::with(['items' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $commutationOptions = DB::table('commutation_options')
            ->orderBy('sort_order')
            ->get();

        $recommendationOptions = DB::table('recommendation_options')
            ->orderBy('sort_order')
            ->get();
        // ─────────────────────────────────────────────────────────

        return view('application.leave-pdf', compact(
            'app',
            'vlBalance',
            'slBalance',
            'allLeaveTypes',
            'detailGroups',
            'commutationOptions',
            'recommendationOptions',
        ));
    }
}