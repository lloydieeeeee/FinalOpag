<?php

namespace App\Http\Controllers;

use App\Models\PayrollRecord;
use App\Models\PayrollPeriod;
use App\Models\PayrollDeduction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    public function manage(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('period_id')->get();
        $selectedPeriodId = $request->query('period_id') ?? optional($periods->first())->period_id;
        $records = collect();

        if ($selectedPeriodId) {
            $records = PayrollRecord::with(['employee.position', 'period.createdBy'])
                ->where('period_id', $selectedPeriodId)
                ->orderBy('user_id')
                ->get();
        }

        return view('payroll.payslip-manage', compact('periods', 'selectedPeriodId', 'records'));
    }

    public function updateRecord(Request $request, $id)
    {
        $record = PayrollRecord::findOrFail($id);
        $period = PayrollPeriod::find($record->period_id);

        if (optional($period)->status === 'FINALIZED') {
            return response()->json(['success' => false, 'error' => 'Period is finalized.'], 403);
        }

        // 1. Update Numeric Amounts
        $numericFields = [
            'gross_salary', 'gsis_ee', 'gsis_ec', 'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
            'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
            'pagibig_ee', 'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity', 'philhealth_ee',
            'withholding_tax', 'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
            'overpayment', 'other_deduction', 'allowance_pera', 'allowance_rata',
            'allowance_ta', 'allowance_other',
        ];

        foreach ($numericFields as $field) {
            if ($request->has($field)) {
                $record->{$field} = round((float) $request->input($field), 2);
            }
        }

        // 2. Update Designation
        if ($request->has('designation')) {
            $record->designation = $request->input('designation');
        }

        // 3. Update Custom Labels
        $labelFields = [
            'label_pera', 'label_rata', 'label_ta', 'label_allowance_other',
            'label_withholding_tax', 'label_gsis_ee', 'label_gsis_ec', 'label_gsis_policy',
            'label_gsis_emergency', 'label_gsis_real_estate', 'label_gsis_mpl', 'label_gsis_mpl_lite',
            'label_gsis_gfal', 'label_gsis_computer', 'label_gsis_conso', 'label_pagibig_govt',
            'label_pagibig_mpl', 'label_pagibig_calamity', 'label_philhealth_ee',
            'label_loan_lbp', 'label_loan_dbp', 'label_loan_cngwmpc', 'label_loan_paracle',
            'overpayment_label', 'other_deduction_label',
        ];

        foreach ($labelFields as $lbl) {
            if ($request->has($lbl)) {
                $record->{$lbl} = substr(trim($request->input($lbl, '')), 0, 100);
            }
        }

        // 4. Handle Dynamic Deductions
        // -------------------------------------------------------------------
        // CRITICAL: dynamic_deductions is cast as 'array' in PayrollRecord.
        // Always assign a plain PHP array — NEVER call json_encode() on it.
        // Manually encoding it causes Eloquent to double-encode the value,
        // and on the next read json_decode() returns null, which makes every
        // foreach() on dynamic_deductions throw:
        //   "foreach() argument must be of type array|object, string given"
        // -------------------------------------------------------------------
        $dynamicInput = $request->input('dynamic', []);
        if (!empty($dynamicInput) && is_array($dynamicInput)) {
            // Cast guarantees this is already a PHP array (or null if the DB column was null)
            $dynamic = is_array($record->dynamic_deductions) ? $record->dynamic_deductions : [];

            foreach ($dynamicInput as $dedId => $amount) {
                $dynamic[(int) $dedId] = round((float) $amount, 2);
            }

            // Assign array directly — Eloquent's 'array' cast handles json_encode on save
            $record->dynamic_deductions = $dynamic;
        }

        // 5. Compute Totals
        $totalDeductions =
            ($record->gsis_ee          ?? 0) +
            ($record->gsis_ec          ?? 0) +
            ($record->gsis_policy      ?? 0) +
            ($record->gsis_emergency   ?? 0) +
            ($record->gsis_real_estate ?? 0) +
            ($record->gsis_mpl         ?? 0) +
            ($record->gsis_mpl_lite    ?? 0) +
            ($record->gsis_gfal        ?? 0) +
            ($record->gsis_computer    ?? 0) +
            ($record->gsis_conso       ?? 0) +
            ($record->pagibig_govt     ?? 0) +
            ($record->pagibig_mpl      ?? 0) +
            ($record->pagibig_calamity ?? 0) +
            ($record->philhealth_ee    ?? 0) +
            ($record->withholding_tax  ?? 0) +
            ($record->loan_dbp         ?? 0) +
            ($record->loan_lbp         ?? 0) +
            ($record->loan_cngwmpc     ?? 0) +
            ($record->loan_paracle     ?? 0) +
            ($record->overpayment      ?? 0) +
            ($record->other_deduction  ?? 0);

        $totalAllowances =
            ($record->allowance_pera  ?? 0) +
            ($record->allowance_rata  ?? 0) +
            ($record->allowance_ta    ?? 0) +
            ($record->allowance_other ?? 0);

        $record->total_deductions = round($totalDeductions, 2);
        $record->total_allowances = round($totalAllowances, 2);
        $record->net_pay          = round(($record->gross_salary ?? 0) - $totalDeductions + $totalAllowances, 2);

        $record->save();

        return response()->json(['success' => true, 'record' => $record]);
    }

    public function printAll(Request $request, $periodId)
    {
        $period  = PayrollPeriod::findOrFail($periodId);
        $records = PayrollRecord::with(['employee.position', 'period'])
            ->where('period_id', $period->period_id)
            ->orderBy('user_id')
            ->get();

        $pdf = Pdf::loadView('payroll.payslip-pdf', ['records' => $records])
            ->setPaper('letter', 'portrait');

        return $pdf->stream("payslips-{$period->period_label}.pdf");
    }

    public function printOne(Request $request, $periodId)
    {
        $period = PayrollPeriod::findOrFail($periodId);
        $userId = $request->query('user_id');

        $records = PayrollRecord::with(['employee.position', 'period'])
            ->where('period_id', $period->period_id)
            ->where('user_id', $userId)
            ->get();

        $pdf = Pdf::loadView('payroll.payslip-pdf', ['records' => $records])
            ->setPaper('letter', 'portrait');

        $name = $records->first()?->employee?->last_name ?? 'Employee';

        return $pdf->stream("payslip-{$name}.pdf");
    }
}