<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PayrollRemittanceController extends Controller
{
    const CNG_FIELDS = [
        'cng_capital_share', 'cng_kiddie_savings', 'cng_savings', 'cng_regular_loan',
        'cng_crisis_loan', 'cng_coop_canteen', 'cng_coop_store', 'cng_calamity_loan',
        'cng_abuloy', 'cng_handog', 'cng_b2b_loan', 'cng_petty_cash', 'cng_commodity_loan'
    ];

    const EDITABLE_FIELDS = [
        'gsis_ee', 'gsis_ec',
        'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
        'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
        'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity',
        'philhealth_ee', 'withholding_tax',
        'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
        'cng_capital_share', 'cng_kiddie_savings', 'cng_savings', 'cng_regular_loan',
        'cng_crisis_loan', 'cng_coop_canteen', 'cng_coop_store', 'cng_calamity_loan',
        'cng_abuloy', 'cng_handog', 'cng_b2b_loan', 'cng_petty_cash', 'cng_commodity_loan',
        'overpayment', 'other_deduction',
        'allowance_pera', 'allowance_rata', 'allowance_ta', 'allowance_other',
    ];

    const DEDUCTION_FIELDS = [
        'gsis_ee', 'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
        'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
        'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity',
        'philhealth_ee', 'withholding_tax',
        'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
        'overpayment', 'other_deduction',
    ];

    // ── FALLBACK MAPPER FOR OLD JSON RECORDS ──
    private function mapOldCngData($records)
    {
        foreach ($records as $r) {
            $dyn = is_string($r->dynamic_deductions) ? json_decode($r->dynamic_deductions, true) : ($r->dynamic_deductions ?? []);
            if (!empty($dyn)) {
                $r->cng_capital_share  = $r->cng_capital_share ?: (float)($dyn[32] ?? 0);
                $r->cng_kiddie_savings = $r->cng_kiddie_savings ?: (float)($dyn[33] ?? 0);
                $r->cng_savings        = $r->cng_savings ?: (float)($dyn[34] ?? 0);
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
        return $records;
    }

    public function index(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();

        $selectedPeriodId = $request->query('period_id', optional($periods->first())->period_id);
        $selectedPeriod = $periods->find($selectedPeriodId);

        $records = collect();

        if ($selectedPeriod) {
            $records = PayrollRecord::with(['employee.position', 'employee.department', 'period'])
                ->where('period_id', $selectedPeriod->period_id)
                ->orderBy(DB::raw("(SELECT last_name FROM employee WHERE employee.employee_id = payroll_record.employee_id)"))
                ->get();
                
            $records = $this->mapOldCngData($records); // <--- Auto-map old data
        }

        $gsis = [
            'ee'          => $records->sum('gsis_ee'),
            'govt'        => $records->sum('gsis_govt'),
            'ec'          => $records->sum('gsis_ec'),
            'mpl'         => $records->sum('gsis_mpl'),
            'policy'      => $records->sum('gsis_policy'),
            'emergency'   => $records->sum('gsis_emergency'),
            'real_estate' => $records->sum('gsis_real_estate'),
            'computer'    => $records->sum('gsis_computer'),
            'gfal'        => $records->sum('gsis_gfal'),
            'mpl_lite'    => $records->sum('gsis_mpl_lite'),
            'conso'       => $records->sum('gsis_conso'),
        ];

        $pagibig = [
            'ee'       => $records->sum('pagibig_ee'),
            'govt'     => $records->sum('pagibig_govt'),
            'mpl'      => $records->sum('pagibig_mpl'),
            'calamity' => $records->sum('pagibig_calamity'),
        ];

        $philhealth = [
            'ee'   => $records->sum('philhealth_ee'),
            'govt' => $records->sum('philhealth_govt'),
        ];

        $loans = [
            'dbp'     => $records->sum('loan_dbp'),
            'lbp'     => $records->sum('loan_lbp'),
            'cngwmpc' => $records->sum('loan_cngwmpc'),
            'paracle' => $records->sum('loan_paracle'),
        ];

        $wtax = $records->sum('withholding_tax');

        return view('payroll.remittances', compact(
            'periods', 'selectedPeriod', 'records',
            'gsis', 'pagibig', 'philhealth', 'loans', 'wtax'
        ));
    }

    public function saveField(Request $request, $id)
    {
        $validated = $request->validate([
            'field' => 'required|string|in:' . implode(',', self::EDITABLE_FIELDS),
            'value' => 'required|numeric|min:0',
        ]);

        $record = PayrollRecord::findOrFail($id);

        if (optional($record->period)->status === 'FINALIZED') {
            return response()->json(['error' => 'Period is finalized.'], 403);
        }

        if ($validated['field'] === 'philhealth_ee') {
            $validated['value'] = min((float) $validated['value'], 2500.00);
        }

        $record->{$validated['field']} = (float) $validated['value'];

        if (in_array($validated['field'], self::CNG_FIELDS)) {
            $cngTotal = 0;
            foreach (self::CNG_FIELDS as $f) {
                $cngTotal += (float) ($record->{$f} ?? 0);
            }
            $record->loan_cngwmpc = $cngTotal;
        }

        $totalDeductions = 0;
        foreach (self::DEDUCTION_FIELDS as $f) {
            $totalDeductions += (float) ($record->{$f} ?? 0);
        }
        $record->total_deductions = $totalDeductions;

        $totalAllowances = (float) ($record->allowance_pera  ?? 0)
                         + (float) ($record->allowance_rata  ?? 0)
                         + (float) ($record->allowance_ta    ?? 0)
                         + (float) ($record->allowance_other ?? 0);
        $record->total_allowances = $totalAllowances;

        if ($validated['field'] === 'philhealth_ee') {
            $record->philhealth_govt = (float) $validated['value'];
        }

        $record->net_pay = max(0, (float) ($record->gross_salary ?? 0) + $totalAllowances - $totalDeductions);
        $record->save();

        return response()->json([
            'success'          => true,
            'total_deductions' => $record->total_deductions,
            'total_allowances' => $record->total_allowances,
            'net_pay'          => $record->net_pay,
            'philhealth_govt'  => $record->philhealth_govt,
            'loan_cngwmpc'     => $record->loan_cngwmpc,
        ]);
    }

    public function hideRecord(Request $request, $id)
    {
        $record = PayrollRecord::find($id);

        if ($record) {
            if (optional($record->period)->status === 'FINALIZED') {
                return response()->json(['error' => 'Period is finalized.'], 403);
            }
            $record->delete();
        }
        return response()->json(['success' => true]);
    }

    public function remittancePdf(Request $request, $periodId, $type)
    {
        $period  = PayrollPeriod::findOrFail($periodId);
        $records = PayrollRecord::with('employee')
            ->where('period_id', $periodId)
            ->get();
            
        $records = $this->mapOldCngData($records); // <--- Auto-map old data for PDF

        $preparedBy = DB::table('signatory_options')->where('label', 'Remittance Signatory')->first();
        $certifiedBy = DB::table('signatory_options')->where('label', 'Provincial Agriculturist')->first();

        $pdf = Pdf::loadView("payroll.remittance-pdf.{$type}", compact('period', 'records', 'preparedBy', 'certifiedBy'))
            ->setPaper('legal', 'landscape');

        return $pdf->stream("remittance_{$type}_{$period->period_label}.pdf");
    }
}