<?php
// app/Http/Controllers/HalfDayController.php
namespace App\Http\Controllers;

use App\Models\HalfDay;
use App\Models\LeaveType;
use App\Models\LeaveCreditBalance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HalfDayController extends Controller
{
    private function getOfficeHead()
    {
        return DB::table('signatory_options')->where('id', 2)->first();
    }

    public function index()
    {
        // $employee contains the user_id (since primaryKey changed in Model)
        $employee    = Auth::user()->employee;
        $currentYear = now()->year;

        $leaveTypes = LeaveType::where('is_active', 1)
            ->whereIn(DB::raw('LOWER(TRIM(type_name))'), ['vacation leave', 'sick leave'])
            ->get();

        $creditBalances = LeaveCreditBalance::where('user_id', $employee->user_id) // ── UPDATED ──
            ->where('year', $currentYear)
            ->get()
            ->keyBy('leave_type_id');

        $halfDays = HalfDay::where('user_id', $employee->user_id) // ── UPDATED ──
            ->with('leaveType')
            ->latest('application_date')
            ->get();

        $officeHead = $this->getOfficeHead(); 

        $leaveRanges = \App\Models\LeaveApplication::where('user_id', $employee->user_id) // ── UPDATED ──
            ->where('is_monetization', 0)
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get(['start_date', 'end_date'])
            ->map(fn($a) => [
                'start' => \Carbon\Carbon::parse($a->start_date)->toDateString(),
                'end'   => \Carbon\Carbon::parse($a->end_date)->toDateString(),
            ])
            ->values()
            ->toArray();

        $existingHalfDays = HalfDay::where('user_id', $employee->user_id) // ── UPDATED ──
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->get(['date_of_absence', 'time_period'])
            ->map(fn($h) => [
                'date'   => \Carbon\Carbon::parse($h->date_of_absence)->toDateString(),
                'period' => $h->time_period,
            ])
            ->values()
            ->toArray();

        return view('application.half-day', compact(
            'employee',
            'leaveTypes',
            'creditBalances',
            'halfDays',
            'leaveRanges',
            'existingHalfDays',
        ))->with('officeHead', $officeHead);
    }

    public function pdf(int $id)
    {
        $employee = Auth::user()->employee;

        $halfDay = HalfDay::with('leaveType')
            ->where('half_day_id', $id)
            ->where('user_id', $employee->user_id) // ── UPDATED ──
            ->firstOrFail();

        $officeHead = $this->getOfficeHead();

        return view('application.halfday-pdf', compact('halfDay', 'employee', 'officeHead'));
    }

    public function cert(int $id)
    {
        $halfDay = HalfDay::with(['employee.department', 'employee.position', 'leaveType'])
            ->findOrFail($id);

        $employee   = $halfDay->employee;
        $officeHead = $this->getOfficeHead();

        return view('application.halfday-pdf', compact('halfDay', 'employee', 'officeHead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id'     => 'required|exists:leave_type,leave_type_id',
            'credit_balance_id' => 'required|exists:leave_credit_balance,credit_balance_id',
            'date_of_absence'   => 'required|date',
            'time_period'       => 'required|in:AM,PM',
            'reason'            => 'nullable|string|max:1000',
        ]);

        $employee  = Auth::user()->employee;
        $leaveType = LeaveType::find($request->leave_type_id);

        if (!$leaveType || !in_array(strtolower(trim($leaveType->type_name)), ['vacation leave', 'sick leave'])) {
            return response()->json(['success' => false, 'message' => 'Only Vacation Leave and Sick Leave are eligible.'], 422);
        }

        $balance = LeaveCreditBalance::where('credit_balance_id', $request->credit_balance_id)
            ->where('user_id', $employee->user_id) // ── UPDATED ──
            ->first();

        if (!$balance) {
            return response()->json(['success' => false, 'message' => 'Invalid leave credit balance.'], 422);
        }
        if ($balance->remaining_balance < 0.5) {
            return response()->json(['success' => false, 'message' => 'Insufficient leave balance.'], 422);
        }

        // Remove old cancelled/rejected duplicates so employee can re-file
        DB::table('half_day')
            ->where('user_id',         $employee->user_id) // ── UPDATED ──
            ->where('date_of_absence', $request->date_of_absence)
            ->where('time_period',     $request->time_period)
            ->whereIn('status',        ['CANCELLED', 'REJECTED'])
            ->delete();

        // Block if ANY active half-day already exists on this date (AM or PM)
        $sameDateExists = HalfDay::where('user_id', $employee->user_id) // ── UPDATED ──
            ->where('date_of_absence', $request->date_of_absence)
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->exists();

        if ($sameDateExists) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a half day application for that date. Only one half day per date is allowed.',
            ], 422);
        }

        $exists = HalfDay::where('user_id',         $employee->user_id) // ── UPDATED ──
            ->where('date_of_absence', $request->date_of_absence)
            ->where('time_period',     $request->time_period)
            ->whereNotIn('status',     ['CANCELLED', 'REJECTED'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => "You already have a {$request->time_period} half day application for that date.",
            ], 422);
        }

        try {
            // ── UPDATED: Insert both user_id to fix constraint! ──
            $halfDayId = DB::table('half_day')->insertGetId([
                'user_id'           => $employee->user_id,
                'employee_id'       => $employee->employee_id,
                'leave_type_id'     => $request->leave_type_id,
                'credit_balance_id' => $request->credit_balance_id,
                'application_date'  => now()->toDateString(),
                'date_of_absence'   => $request->date_of_absence,
                'time_period'       => $request->time_period,
                'reason'            => $request->reason,
                'status'            => 'PENDING',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $empName = strtoupper($employee->last_name) . ', ' . $employee->first_name;
            $ltName  = $leaveType->type_name;
            $absDate = \Carbon\Carbon::parse($request->date_of_absence)->format('M d, Y');

            $adminIds = DB::table('user_access')
                ->where('user_access', 'admin')
                ->where('is_active', 1)
                ->pluck('employee_id');

            $now = now();
            foreach ($adminIds as $adminEmpId) {
                DB::table('notifications')->insert([
                    'recipient_id'   => $adminEmpId,
                    'sender_id'      => $employee->employee_id, // Legacy sender tracking
                    'type'           => 'halfday_pending',
                    'title'          => 'New Half Day Application',
                    'message'        => "{$empName} filed a {$request->time_period} half day ({$ltName}) for {$absDate}.",
                    'reference_id'   => $halfDayId,
                    'reference_type' => 'half_day',
                    'is_read'        => 0,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }

            return response()->json([
                'success'     => true,
                'half_day_id' => $halfDayId,
                'message'     => 'Half day certification filed successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('HalfDay store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save: ' . $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $employee = Auth::user()->employee;
        $halfDay  = HalfDay::where('half_day_id', $id)
            ->where('user_id', $employee->user_id) // ── UPDATED ──
            ->firstOrFail();

        if ($halfDay->status !== 'PENDING') {
            return response()->json(['success' => false, 'message' => 'Only pending applications can be cancelled.'], 422);
        }

        $halfDay->update(['status' => 'CANCELLED']);

        return response()->json(['success' => true, 'message' => 'Half day certification cancelled.']);
    }
}