<?php
// app/Http/Controllers/EmployeeController.php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use App\Models\UserCredential;
use App\Models\UserAccess;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveApplication;
use App\Models\HalfDay;
use App\Models\PayrollRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['position', 'department', 'credential', 'access'])
                             ->orderBy('last_name')
                             ->get();

        $positions   = Position::where('is_active', 1)->orderBy('position_name')->get();
        $departments = Department::where('is_active', 1)->orderBy('department_name')->get();
        $roleOptions = DB::table('role_options')->orderBy('sort_order')->orderBy('id')->get();

        $employeesJson = $employees->keyBy('employee_id')->map(function ($e) {
            return [
                'employee_id'    => $e->employee_id,
                'formatted_id'   => $e->formatted_employee_id,
                'first_name'     => $e->first_name,
                'middle_name'    => $e->middle_name,
                'last_name'      => $e->last_name,
                'extension_name' => $e->extension_name ?? null,
                'birthday'       => $e->birthday,
                'contact_number' => $e->contact_number,
                'address'        => $e->address,
                'hire_date'      => $e->hire_date,
                'department_id'  => $e->department_id,
                'position_id'    => $e->position_id,
                'salary'         => $e->salary,
                'is_active'      => $e->is_active,
                'pagibig_id'     => $e->pagibig_id,
                'gsis_id'        => $e->gsis_id,
                'philhealth_id'  => $e->philhealth_id,
                'tin'            => $e->tin,
                'credential'     => $e->credential ? ['username' => $e->credential->username] : null,
                'access'         => $e->access ? ['user_access' => $e->access->user_access] : null,
            ];
        });

        return view('employees', compact(
            'employees', 'positions', 'departments', 'employeesJson', 'roleOptions'
        ));
    }

    public function show($id)
    {
        try {
            $employee = Employee::with(['department', 'position', 'access', 'credential'])->findOrFail($id);

            $leaveBalances = LeaveCreditBalance::with('leaveType')
                ->where('employee_id', $id)
                ->orderByDesc('year')->orderBy('leave_type_id')->get()
                ->map(fn($b) => [
                    'credit_balance_id' => $b->credit_balance_id,
                    'leave_type_id'     => $b->leave_type_id,
                    'leave_type'        => optional($b->leaveType)->type_name ?? '—',
                    'type_code'         => optional($b->leaveType)->type_code ?? '—',
                    'year'              => (string) $b->year,
                    'total_accrued'     => (string) $b->getRawOriginal('total_accrued'),
                    'total_used'        => (string) $b->getRawOriginal('total_used'),
                    'remaining_balance' => (string) $b->getRawOriginal('remaining_balance'),
                ]);

            $leaveApplications = LeaveApplication::with('leaveType')
                ->where('employee_id', $id)->orderByDesc('created_at')->get()
                ->map(fn($la) => [
                    'leave_id'         => $la->leave_id,
                    'leave_type'       => optional($la->leaveType)->type_name ?? '—',
                    'type_code'        => optional($la->leaveType)->type_code ?? '—',
                    'application_date' => $la->application_date ? \Carbon\Carbon::parse($la->application_date)->format('Y-m-d') : null,
                    'start_date'       => $la->start_date ? \Carbon\Carbon::parse($la->start_date)->format('Y-m-d') : null,
                    'end_date'         => $la->end_date   ? \Carbon\Carbon::parse($la->end_date)->format('Y-m-d')   : null,
                    'no_of_days'       => $la->no_of_days,
                    'status'           => $la->status,
                    'is_monetization'  => (bool) $la->is_monetization,
                    'commutation'      => $la->commutation,
                    'reject_reason'    => $la->reject_reason,
                ]);

            $halfDayApplications = HalfDay::with('leaveType')
                ->where('employee_id', $id)->orderByDesc('created_at')->get()
                ->map(fn($hd) => [
                    'half_day_id'     => $hd->half_day_id,
                    'leave_type'      => optional($hd->leaveType)->type_name ?? '—',
                    'type_code'       => optional($hd->leaveType)->type_code ?? '—',
                    'date_of_absence' => $hd->date_of_absence ? \Carbon\Carbon::parse($hd->date_of_absence)->format('Y-m-d') : null,
                    'time_period'     => $hd->time_period,
                    'status'          => $hd->status,
                    'reason'          => $hd->reason,
                ]);

            $payrollRecords = PayrollRecord::with('period')
                ->where('employee_id', $id)->orderByDesc('period_id')->get()
                ->map(fn($pr) => [
                    'payroll_id'       => $pr->getKey(),
                    'period_label'     => optional($pr->period)->period_label
                                         ?? (optional($pr->period) ? \Carbon\Carbon::createFromDate(null, $pr->period->month, 1)->format('F').' '.$pr->period->year : '—'),
                    'gross_salary'     => $pr->gross_salary,
                    'gsis_ee'          => $pr->gsis_ee,
                    'gsis_govt'        => $pr->gsis_govt,
                    'pagibig_ee'       => $pr->pagibig_ee,
                    'pagibig_govt'     => $pr->pagibig_govt,
                    'philhealth_ee'    => $pr->philhealth_ee,
                    'philhealth_govt'  => $pr->philhealth_govt,
                    'withholding_tax'  => $pr->withholding_tax,
                    'loan_dbp'         => $pr->loan_dbp,
                    'loan_lbp'         => $pr->loan_lbp,
                    'loan_cngwmpc'     => $pr->loan_cngwmpc,
                    'loan_paracle'     => $pr->loan_paracle,
                    'allowance_pera'   => $pr->allowance_pera,
                    'allowance_rata'   => $pr->allowance_rata,
                    'allowance_other'  => $pr->allowance_other,
                    'total_deductions' => $pr->total_deductions,
                    'total_allowances' => $pr->total_allowances,
                    'net_pay'          => $pr->net_pay,
                ]);

            $id7   = str_pad($employee->employee_id, 7, '0', STR_PAD_LEFT);
            $fmtId = substr($id7, 0, 3) . '-' . substr($id7, 3);

            return response()->json([
                'success'  => true,
                'employee' => [
                    'employee_id'     => $employee->employee_id,
                    'formatted_id'    => $fmtId,
                    'first_name'      => $employee->first_name,
                    'middle_name'     => $employee->middle_name,
                    'last_name'       => $employee->last_name,
                    'extension_name'  => $employee->extension_name,
                    'birthday'        => $employee->birthday,
                    'contact_number'  => $employee->contact_number,
                    'address'         => $employee->address,
                    'hire_date'       => $employee->hire_date,
                    'salary'          => $employee->salary,
                    'is_active'       => $employee->is_active,
                    'department_name' => optional($employee->department)->department_name ?? '—',
                    'position_name'   => optional($employee->position)->position_name    ?? '—',
                    'user_access'     => optional($employee->access)->user_access        ?? null,
                    'username'        => optional($employee->credential)->username       ?? null,
                    'pagibig_id'      => $employee->pagibig_id,
                    'gsis_id'         => $employee->gsis_id,
                    'philhealth_id'   => $employee->philhealth_id,
                    'tin'             => $employee->tin,
                ],
                'leaveBalances'       => $leaveBalances,
                'leaveApplications'   => $leaveApplications,
                'halfDayApplications' => $halfDayApplications,
                'payrollRecords'      => $payrollRecords,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|digits:7|unique:employee,employee_id',
            'first_name'    => 'required|string|max:80',
            'last_name'     => 'required|string|max:80',
            'hire_date'     => 'required|date',
            'department_id' => 'required|exists:department,department_id',
            'position_id'   => 'required|exists:position,position_id',
            'salary'        => 'required|numeric|min:0',
            'username'      => 'required|string|max:60|unique:user_credentials,username',
            'password'      => 'required|string|min:6',
            'pagibig_id'    => 'nullable|numeric',
            'gsis_id'       => 'nullable|numeric',
            'philhealth_id' => 'nullable|numeric',
            'tin'           => 'nullable|string|max:20',
            'vl_balance'    => 'nullable|numeric|min:0',
            'sl_balance'    => 'nullable|numeric|min:0',
        ], [
            'employee_id.digits' => 'Employee ID must be exactly 7 digits.',
            'employee_id.unique' => 'This Employee ID is already taken.',
            'username.unique'    => 'This username is already taken.',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $employee = Employee::create([
                    'employee_id'    => $request->employee_id,
                    'first_name'     => strtoupper(trim($request->first_name)),
                    'middle_name'    => $request->middle_name ? strtoupper(trim($request->middle_name)) : null,
                    'last_name'      => strtoupper(trim($request->last_name)),
                    'extension_name' => $request->extension_name ?: null,
                    'birthday'       => $request->birthday        ?: null,
                    'contact_number' => $request->contact_number  ?: null,
                    'address'        => $request->address          ?: null,
                    'hire_date'      => $request->hire_date,
                    'department_id'  => $request->department_id,
                    'position_id'    => $request->position_id,
                    'salary'         => $request->salary,
                    'is_active'      => 1,
                    'pagibig_id'     => $request->pagibig_id   ?: null,
                    'gsis_id'        => $request->gsis_id       ?: null,
                    'philhealth_id'  => $request->philhealth_id ?: null,
                    'tin'            => $request->tin           ?: null,
                ]);

                UserCredential::create([
                    'employee_id'   => $employee->employee_id,
                    'username'      => $request->username,
                    'password_hash' => Hash::make($request->password),
                    'is_active'     => 1,
                ]);

                UserAccess::create([
                    'employee_id' => $employee->employee_id,
                    'user_access' => $request->user_access ?? 'employee',
                    'is_active'   => 1,
                ]);

                Log::info('About to insert leave balances', [
                    'employee_id'     => $employee->employee_id,
                    'is_new_employee' => $request->input('is_new_employee'),
                    'vl_balance'      => $request->input('vl_balance'),
                    'sl_balance'      => $request->input('sl_balance'),
                    'year'            => date('Y'),
                ]);

                $currentYear = (int) date('Y');
                $vlAmount    = round((float) $request->input('vl_balance', 0), 4);
                $slAmount    = round((float) $request->input('sl_balance', 0), 4);

                // ── Vacation Leave (leave_type_id = 1) ──
                LeaveCreditBalance::create([
                    'employee_id'       => $employee->employee_id,
                    'leave_type_id'     => 1,
                    'year'              => $currentYear,
                    'total_accrued'     => $vlAmount,
                    'total_used'        => 0,
                    'remaining_balance' => $vlAmount,
                    'vacation_balance'  => 0,
                    'sick_balance'      => 0,
                ]);

                // ── Sick Leave (leave_type_id = 2) ──
                LeaveCreditBalance::create([
                    'employee_id'       => $employee->employee_id,
                    'leave_type_id'     => 2,
                    'year'              => $currentYear,
                    'total_accrued'     => $slAmount,
                    'total_used'        => 0,
                    'remaining_balance' => $slAmount,
                    'vacation_balance'  => 0,
                    'sick_balance'      => 0,
                ]);

                // ── Seed old_balance so the leave card opening balance is
                //    permanently fixed at hire time and never drifts when
                //    the leave card is edited later.
                //    Works for BOTH new employees (computed from hire date)
                //    and old/existing employees (manually entered balance).
                //    pdf_file = null distinguishes auto-seeded records from
                //    uploaded prior-office PDF records.
                DB::table('old_balance')->updateOrInsert(
                [
                    'employee_id'    => $employee->employee_id,
                    'reference_year' => $currentYear,
                ],
                [
                    'old_vl_balance' => $vlAmount,
                    'old_sl_balance' => $slAmount,
                    'pdf_file'       => null,
                    'created_at'     => now(),
                ]
            );

                // ── Pre-create the leave card with opening balance fixed at
                //    hire time. LeaveCardController@show finds this card
                //    immediately and reads opening_vl/opening_sl from it —
                //    never reading leave_credit_balance for opening balance.
                //    leave_credit_balance keeps changing as leave is used.
                //    leave_card.opening_vl/sl never changes after this point.
                DB::table('leave_card')->insert([
                    'employee_id' => $employee->employee_id,
                    'year'        => $currentYear,
                    'opening_vl'  => $vlAmount,
                    'opening_sl'  => $slAmount,
                    'created_by'  => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

            });

        } catch (\Throwable $e) {
            Log::error('Employee store failed', [
                'message'     => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
                'employee_id' => $request->employee_id,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save employee: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Employee added successfully.']);
        }

        return redirect()->route('employees')->with('success', 'Employee added successfully.');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $credId   = optional($employee->credential)->credential_id;

        $request->validate([
            'first_name'    => 'required|string|max:80',
            'last_name'     => 'required|string|max:80',
            'hire_date'     => 'required|date',
            'department_id' => 'required|exists:department,department_id',
            'position_id'   => 'required|exists:position,position_id',
            'salary'        => 'required|numeric|min:0',
            'username'      => 'nullable|string|max:60|unique:user_credentials,username,' . $credId . ',credential_id',
            'password'      => 'nullable|string|min:6',
            'pagibig_id'    => 'nullable|numeric',
            'gsis_id'       => 'nullable|numeric',
            'philhealth_id' => 'nullable|numeric',
            'tin'           => 'nullable|string|max:20',
        ], [
            'username.unique' => 'This username is already taken by another account.',
        ]);

        $employee->update([
            'first_name'     => strtoupper(trim($request->first_name)),
            'middle_name'    => $request->middle_name ? strtoupper(trim($request->middle_name)) : null,
            'last_name'      => strtoupper(trim($request->last_name)),
            'extension_name' => $request->extension_name ?: null,
            'birthday'       => $request->birthday        ?: null,
            'contact_number' => $request->contact_number  ?: null,
            'address'        => $request->address          ?: null,
            'hire_date'      => $request->hire_date,
            'department_id'  => $request->department_id,
            'position_id'    => $request->position_id,
            'salary'         => $request->salary,
            'pagibig_id'     => $request->pagibig_id    ?: null,
            'gsis_id'        => $request->gsis_id        ?: null,
            'philhealth_id'  => $request->philhealth_id  ?: null,
            'tin'            => $request->tin            ?: null,
        ]);

        if ($employee->credential) {
            $credData = [];
            if ($request->filled('username')) $credData['username']      = $request->username;
            if ($request->filled('password')) $credData['password_hash'] = Hash::make($request->password);
            if (!empty($credData)) $employee->credential->update($credData);
        }

        if ($employee->access) {
            $employee->access->update(['user_access' => $request->user_access ?? 'employee']);
        } else {
            UserAccess::create([
                'employee_id' => $employee->employee_id,
                'user_access' => $request->user_access ?? 'employee',
                'is_active'   => 1,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Employee updated successfully.']);
        }

        return redirect()->route('employees')->with('success', 'Employee updated.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $employee  = Employee::findOrFail($id);
        $newStatus = $employee->is_active ? 0 : 1;

        $employee->update(['is_active' => $newStatus]);

        if ($employee->credential) {
            $employee->credential->update(['is_active' => $newStatus]);
        }

        $msg = $newStatus
            ? 'Employee has been activated and can now log in.'
            : 'Employee has been deactivated and cannot log in.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'is_active' => $newStatus]);
        }

        return redirect()->route('employees')->with('success', $msg);
    }
}