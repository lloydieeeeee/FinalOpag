<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PayrollDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    const CNG_FIELDS = [
        'cng_capital_share', 'cng_kiddie_savings', 'cng_savings', 'cng_regular_loan',
        'cng_crisis_loan', 'cng_coop_canteen', 'cng_coop_store', 'cng_calamity_loan',
        'cng_abuloy', 'cng_handog', 'cng_b2b_loan', 'cng_petty_cash', 'cng_commodity_loan'
    ];

    private function isAdmin(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if (isset($user->access) && strtolower($user->access->user_access ?? '') === 'admin') return true;
        if (isset($user->userAccess) && strtolower($user->userAccess->user_access ?? '') === 'admin') return true;
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        if (isset($user->user_access) && strtolower($user->user_access) === 'admin') return true;
        if (isset($user->role) && strtolower($user->role) === 'admin') return true;

        $empId = $user->employee_id ?? optional($user->employee)->employee_id ?? null;
        if ($empId) {
            $row = DB::table('user_access')->where('employee_id', $empId)->where('is_active', 1)->value('user_access');
            if ($row && strtolower($row) === 'admin') return true;
        }

        return false;
    }

    private function isGovtShareColumn(?string $col): bool
    {
        return in_array($col, ['gsis_govt', 'philhealth_govt', 'pagibig_govt'], true);
    }

    private function normaliseOverrides(array $overrides): array
    {
        $out = [];
        foreach ($overrides as $key => $value) {
            if ($key === 'allowance_ra') {
                $out['allowance_rata'] = $value;
            } else {
                $out[$key] = $value;
            }
        }
        return $out;
    }

    private function mapOldCngData($records)
    {
        if (empty($records)) return $records;

        $isSingle = false;
        if ($records instanceof \App\Models\PayrollRecord) {
            $records = [$records];
            $isSingle = true;
        }

        foreach ($records as $r) {
            if (!$r) continue;
            $dyn = is_string($r->dynamic_deductions) ? json_decode($r->dynamic_deductions, true) : ($r->dynamic_deductions ?? []);
            if (!empty($dyn)) {
                $r->cng_capital_share  = $r->cng_capital_share ?: (float)($dyn[32] ?? 0);
                $r->cng_kiddie_savings = $r->cng_kiddie_savings ?: (float)($dyn[33] ?? 0);
                $r->cng_savings         = $r->cng_savings ?: (float)($dyn[34] ?? 0);
                $r->cng_regular_loan   = $r->cng_regular_loan ?: (float)($dyn[35] ?? 0);
                $r->cng_crisis_loan    = $r->cng_crisis_loan ?: (float)($dyn[36] ?? 0);
                $r->cng_coop_canteen   = $r->cng_coop_canteen ?: (float)($dyn[37] ?? 0);
                $r->cng_coop_store     = $r->cng_coop_store ?: (float)($dyn[38] ?? 0);
                $r->cng_calamity_loan  = $r->cng_calamity_loan ?: (float)($dyn[39] ?? 0);
                $r->cng_abuloy         = $r->cng_abuloy ?: (float)($dyn[40] ?? 0);
                $r->cng_handog         = $r->cng_handog ?: (float)($dyn[41] ?? 0);
                $r->cng_b2b_loan       = $r->cng_b2b_loan ?: (float)($dyn[42] ?? 0);
                $r->cng_petty_cash     = $r->cng_petty_cash ?: (float)($dyn[43] ?? 0);
                $r->cng_commodity_loan = $r->cng_commodity_loan ?: (float)($dyn[44] ?? 0);
                
                $cngSum = $r->cng_capital_share + $r->cng_kiddie_savings + $r->cng_savings + 
                          $r->cng_regular_loan + $r->cng_crisis_loan + $r->cng_coop_canteen + 
                          $r->cng_coop_store + $r->cng_calamity_loan + $r->cng_abuloy + 
                          $r->cng_handog + $r->cng_b2b_loan + $r->cng_petty_cash + $r->cng_commodity_loan;
                
                if ($cngSum > 0 && !$r->loan_cngwmpc) {
                    $r->loan_cngwmpc = $cngSum;
                }
            }
        }
        return $isSingle ? $records[0] : $records;
    }

    public function computeFromSalary(float $gross, array $overrides = [], Employee $employee = null): array
    {
        $overrides = $this->normaliseOverrides($overrides);
        $deductions = PayrollDeduction::active()->ordered()->get();
        $hardFields    = [];
        $dynamicFields = [];
        $totalDeductions = 0.0;
        $totalAllowances = 0.0;

        $isPA = false;
        if ($employee && $employee->position) {
            $isPA = (strtoupper(trim($employee->position->position_code)) === 'PA');
        }

        foreach ($deductions as $ded) {
            if ($ded->parent_id == 9 || $ded->name === 'CNGWPC') continue;

            $col   = $ded->resolveColumn();
            $isDyn = ($col === null);
            $isAll = $ded->isAllowance();

            if ($ded->isFixed()) {
                if (!$isDyn && array_key_exists($col, $overrides)) {
                    $amount = (float) $overrides[$col];
                } elseif ($isDyn && array_key_exists($ded->id, $overrides)) {
                    $amount = (float) $overrides[$ded->id];
                } else {
                    if ($col === 'allowance_rata' || $col === 'allowance_ta') {
                        $amount = $isPA ? $ded->compute($gross) : 0.0;
                    } else {
                        $amount = $ded->compute($gross);
                    }
                }
            } else {
                if (!$isDyn && $col !== null && array_key_exists($col, $overrides)) $amount = (float) $overrides[$col];
                elseif ($isDyn && array_key_exists($ded->id, $overrides)) $amount = (float) $overrides[$ded->id];
                else $amount = 0.0;
            }

            $amount = round($amount, 2);
            if ($isDyn) $dynamicFields[$ded->id] = $amount;
            else $hardFields[$col] = ($hardFields[$col] ?? 0) + $amount;

            if ($isAll) $totalAllowances += $amount;
            elseif (!$this->isGovtShareColumn($col)) $totalDeductions += $amount;
        }

        $cngTotal = 0;
        foreach (self::CNG_FIELDS as $f) {
            $val = isset($overrides[$f]) ? round((float) $overrides[$f], 2) : 0.0;
            $hardFields[$f] = $val;
            $totalDeductions += $val;
            $cngTotal += $val;
        }
        $hardFields['loan_cngwmpc'] = $cngTotal;

        $hardDefaults = [
            'gsis_ee' => 0, 'gsis_govt' => 0, 'gsis_ec' => 0, 'gsis_policy' => 0, 'gsis_emergency' => 0, 'gsis_real_estate' => 0,
            'gsis_mpl' => 0, 'gsis_mpl_lite' => 0, 'gsis_gfal' => 0, 'gsis_computer' => 0, 'gsis_conso' => 0,
            'pagibig_ee' => 0, 'pagibig_govt' => 0, 'pagibig_mpl' => 0, 'pagibig_calamity' => 0,
            'philhealth_ee' => 0, 'philhealth_govt' => 0, 'withholding_tax' => 0,
            'loan_dbp' => 0, 'loan_lbp' => 0, 'loan_paracle' => 0, 'overpayment' => 0, 'other_deduction' => 0,
            'allowance_pera' => 0, 'allowance_rata' => 0, 'allowance_ta' => 0, 'allowance_other' => 0,
        ];

        $hardFields = array_merge($hardDefaults, $hardFields);
        $totalDeductions = round($totalDeductions, 2);
        $totalAllowances = round($totalAllowances, 2);
        $netPay          = round($gross - $totalDeductions + $totalAllowances, 2);

        return array_merge($hardFields, [
            'gross_salary'       => $gross,
            'dynamic_deductions' => empty($dynamicFields) ? null : $dynamicFields,
            'total_deductions'   => $totalDeductions,
            'total_allowances'   => $totalAllowances,
            'net_pay'            => $netPay,
        ]);
    }

    private function generatePayrollForPeriod(PayrollPeriod $period, array $employeeIds = [], array $overrides = []): int {
        $query = Employee::where('is_active', 1)->where('salary', '>', 0);
        if (!empty($employeeIds)) $query->whereIn('employee_id', $employeeIds);

        $count = 0;
        PayrollRecord::unguard();
        $query->each(function (Employee $emp) use ($period, $overrides, &$count) {
            $empOverrides = $overrides[$emp->employee_id] ?? [];
            $data = $this->computeFromSalary((float) $emp->salary, $empOverrides, $emp);
            $data['designation'] = optional($emp->position)->position_code;

            PayrollRecord::updateOrCreate(
                ['period_id' => $period->period_id, 'employee_id' => $emp->employee_id],
                $data
            );
            $count++;
        });
        PayrollRecord::reguard();
        return $count;
    }

    public function index(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();
        $selectedPeriodId = $request->input('period_id', optional($periods->first())->period_id);
        $selectedPeriod = $periods->find($selectedPeriodId);

        if ($selectedPeriod && $selectedPeriod->status === 'FINALIZED') {
            if (PayrollRecord::where('period_id', $selectedPeriod->period_id)->count() === 0) {
                $this->generatePayrollForPeriod($selectedPeriod);
            }
        }

        $records = PayrollRecord::with(['employee.position', 'employee.department'])
            ->where('period_id', optional($selectedPeriod)->period_id)
            ->orderBy('employee_id')
            ->get();
            
        $records = $this->mapOldCngData($records); 

        $summary = (object) [
            'employees'  => $records->count(),
            'gross'      => $records->sum('gross_salary'),
            'deductions' => $records->sum('total_deductions'),
            'net'        => $records->sum('net_pay'),
            'wtax'       => $records->sum('withholding_tax'),
            'gsis_ee'    => $records->sum('gsis_ee'),
            'gsis_govt'  => $records->sum('gsis_govt'),
            'gsis_ec'    => $records->sum('gsis_ec'),
            'pagibig'    => $records->sum('pagibig_govt'),
            'philhealth' => $records->sum('philhealth_ee'),
            'pera_total' => $records->sum('allowance_pera'),
        ];

        $jsConfig = PayrollDeduction::buildJsConfig();
        return view('payroll.index', compact('periods', 'selectedPeriod', 'records', 'summary', 'jsConfig'));
    }

    public function create()
    {
        $jsConfig = PayrollDeduction::buildJsConfig();
        return view('payroll.create', compact('jsConfig'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month'          => 'required|integer|between:1,12',
            'year'           => 'required|integer|min:2000|max:2100',
            'period_label'   => 'required|string|max:100',
            'employee_ids'   => 'required|array|min:1',
            'employee_ids.*' => 'integer',
        ]);

        $existing = PayrollPeriod::where('month', $request->month)->where('year', $request->year)->first();
        if ($existing) {
            return redirect()->route('payroll.index', ['period_id' => $existing->period_id])
                             ->with('error', "A payroll period for {$existing->period_label} already exists.");
        }

        $period = PayrollPeriod::create([
            'period_label' => $request->period_label, 'month' => $request->month,
            'year' => $request->year, 'status' => 'DRAFT',
            'created_by' => Auth::user()?->employee?->employee_id,
        ]);

        $rawOverrides = $request->input('overrides', []);
        $overrides = [];
        foreach ($rawOverrides as $empId => $fields) {
            $overrides[$empId] = $this->normaliseOverrides((array) $fields);
        }

        $this->generatePayrollForPeriod($period, $request->employee_ids, $overrides);

        return redirect()->route('payroll.index', ['period_id' => $period->period_id])
                         ->with('success', "Payroll for {$period->period_label} generated successfully.");
    }

    public function finalize($period)
    {
        if (!$period instanceof PayrollPeriod) $period = PayrollPeriod::findOrFail($period);
        if ($period->status === 'FINALIZED') return back()->with('error', 'Period is already finalized.');

        if (PayrollRecord::where('period_id', $period->period_id)->count() === 0) {
            $this->generatePayrollForPeriod($period);
        }

        $period->update(['status' => 'FINALIZED']);
        return redirect()->route('payroll.index', ['period_id' => $period->period_id])
                         ->with('success', "Payroll for {$period->period_label} has been finalized.");
    }

    public function updateRecord(Request $request, $id)
    {
        $record = PayrollRecord::findOrFail($id);
        $period = PayrollPeriod::find($record->period_id);

        if (optional($period)->status === 'FINALIZED') {
            return response()->json(['success' => false, 'error' => 'Period is finalized.'], 403);
        }

        $hardNumeric = [
            'gross_salary', 'gsis_ee', 'gsis_ec', 'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
            'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
            'pagibig_ee', 'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity', 'philhealth_ee', 'withholding_tax',
            'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
            'cng_capital_share', 'cng_kiddie_savings', 'cng_savings', 'cng_regular_loan',
            'cng_crisis_loan', 'cng_coop_canteen', 'cng_coop_store', 'cng_calamity_loan',
            'cng_abuloy', 'cng_handog', 'cng_b2b_loan', 'cng_petty_cash', 'cng_commodity_loan',
            'overpayment', 'other_deduction', 'allowance_pera', 'allowance_rata', 'allowance_ta', 'allowance_other',
        ];

        foreach ($hardNumeric as $col) {
            if ($request->has($col)) $record->$col = round((float) $request->input($col), 2);
        }
        if ($request->has('allowance_ra')) $record->allowance_rata = round((float) $request->input('allowance_ra'), 2);
        
        // ADDED BACK: Save all your custom text labels that were missing!
        $labels = [
            'label_loan_lbp', 'label_loan_dbp', 'label_loan_cngwmpc', 'label_loan_paracle',
            'label_pera', 'label_rata', 'label_ta', 'label_allowance_other', 'other_deduction_label'
        ];
        foreach ($labels as $lbl) {
            if ($request->has($lbl)) {
                $record->$lbl = substr(trim($request->input($lbl, '')), 0, 100);
            }
        }

        $cngSum = 0;
        foreach(self::CNG_FIELDS as $f) { $cngSum += (float) $record->$f; }
        $record->loan_cngwmpc = $cngSum;

        $dynamicInput = $request->input('dynamic', []);
        if (!empty($dynamicInput)) {
            $dynamic = $record->dynamic_deductions ?? [];
            foreach ($dynamicInput as $dedId => $amount) { $dynamic[(int) $dedId] = round((float) $amount, 2); }
            $record->dynamic_deductions = $dynamic;
        }

        if (method_exists($record, 'recomputeTotals')) {
            $deductionConfig = PayrollDeduction::active()->ordered()->get()->keyBy('id');
            $record->recomputeTotals($deductionConfig);
        }
        $record->save();

        return response()->json(['success' => true, 'record' => $record]);
    }

    public function pdf($period)
    {
        if (!$period instanceof PayrollPeriod) $period = PayrollPeriod::findOrFail($period);
        $records = PayrollRecord::with(['employee.position'])
            ->where('period_id', $period->period_id)
            ->orderBy('employee_id')
            ->get();
        $records = $this->mapOldCngData($records); 

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('legal', 'landscape');
        $pdf->loadView('payroll.payroll-pdf', compact('period', 'records'));
        return $pdf->stream("payroll_{$period->period_label}.pdf");
    }

    public function payslipAllPdf(Request $request, $id)
    {
        $period = PayrollPeriod::findOrFail($id);
        $query = PayrollRecord::with(['employee', 'period'])
            ->where('period_id', $period->period_id)
            ->orderBy('employee_id');

        if ($empId = $request->input('emp_id')) $query->where('employee_id', $empId);

        $records = $query->get();
        $records = $this->mapOldCngData($records); 

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('letter', 'portrait');
        $pdf->loadView('payroll.payslip-pdf', compact('records'));
        return $pdf->stream("payslips_{$period->period_label}.pdf");
    }

    public function payslipPdf(Request $request, $period)
    {
        $periodObj = PayrollPeriod::findOrFail($period);
        $user = Auth::user();
        
        $empId = $request->input('emp_id', optional($user->employee)->employee_id);

        $records = PayrollRecord::with(['employee', 'period'])
            ->where('period_id', $periodObj->period_id)
            ->where('employee_id', $empId)
            ->get();
            
        $records = $this->mapOldCngData($records); 

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('letter', 'portrait');
        $pdf->loadView('payroll.payslip-pdf', compact('records'));
        return $pdf->stream("payslip_{$periodObj->period_label}.pdf");
    }

    public function payslip(Request $request)
    {
        if ($this->isAdmin()) {
            $periodId = $request->input('period_id');
            return redirect()->route('payroll.manage', $periodId ? ['period_id' => $periodId] : []);
        }

        $user = Auth::user();
        $employee = $user?->employee;
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();
        $selectedPeriodId = $request->input('period_id', optional($periods->first())->period_id);

        $record = $employee
            ? PayrollRecord::with(['employee', 'period'])
                ->where('employee_id', $employee->employee_id)
                ->where('period_id', $selectedPeriodId)
                ->first()
            : null;
            
        $record = $this->mapOldCngData($record);
        return view('payroll.payslip', compact('periods', 'selectedPeriodId', 'record'));
    }

    public function updateSignatory(Request $request, $period)
    {
        if (!$period instanceof PayrollPeriod) $period = PayrollPeriod::findOrFail($period);
        $request->validate(['sig_clerk_name' => 'nullable|string|max:100', 'sig_clerk_title' => 'nullable|string|max:100']);
        $period->update([
            'sig_clerk_name'  => strtoupper(trim($request->input('sig_clerk_name',  ''))),
            'sig_clerk_title' => trim($request->input('sig_clerk_title', '')),
        ]);
        return response()->json(['success' => true, 'period' => $period]);
    }

    public function manage(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('period_id')->get();
        $selectedPeriodId = $request->query('period_id') ?? optional($periods->first())->period_id;
        $records = collect();

        if ($selectedPeriodId) {
            $records = PayrollRecord::with(['employee.position', 'period.createdBy'])
                ->where('period_id', $selectedPeriodId)
                ->orderBy('employee_id')
                ->get();
            $records = $this->mapOldCngData($records); 
        }

        return view('payroll.payslip-manage', compact('periods', 'selectedPeriodId', 'records'));
    }

    // ── RESTORED REMITTANCES METHODS ──
    public function remittances(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();
        $selectedPeriodId = $request->input('period_id', optional($periods->first())->period_id);
        $selectedPeriod = $periods->find($selectedPeriodId);

        $records = collect();
        if ($selectedPeriodId) {
            $records = PayrollRecord::with(['employee.position', 'employee.department'])
                ->where('period_id', $selectedPeriodId)
                ->orderBy('employee_id')
                ->get();
            $records = $this->mapOldCngData($records); 
        }

        return view('payroll.remittances', compact('periods', 'selectedPeriod', 'records'));
    }

    public function remittancePdf($periodId, $type)
    {
        $period = PayrollPeriod::findOrFail($periodId);
        $records = PayrollRecord::with(['employee.position'])
            ->where('period_id', $period->period_id)
            ->orderBy('employee_id')
            ->get();
            
        $records = $this->mapOldCngData($records); 

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('legal', 'landscape');
        $pdf->loadView('payroll.remittance-pdf', compact('period', 'records', 'type'));
        
        return $pdf->stream("remittance_{$type}_{$period->period_label}.pdf");
    }
}