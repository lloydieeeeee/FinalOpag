<?php

namespace App\Http\Controllers;

use App\Models\PayrollRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    public function manage(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('period_id')->get();

        $selectedPeriodId = $request->query('period_id')
            ?? optional($periods->first())->period_id;

        $records = collect();

        if ($selectedPeriodId) {
            $records = PayrollRecord::with([
                    'employee.position',
                    'period.createdBy',
                ])
                ->where('period_id', $selectedPeriodId)
                ->orderBy('employee_id')
                ->get();
        }

        return view('payroll.payslip-manage', compact('periods', 'selectedPeriodId', 'records'));
    }

    public function updateRecord(Request $request, $id)
    {
        $record = PayrollRecord::findOrFail($id);
        $period = PayrollPeriod::find($record->period_id);

        if (optional($period)->status === 'FINALIZED') {
            return response()->json([
                'success' => false,
                'error'   => 'This payroll period is finalized and cannot be edited.',
            ], 403);
        }

        // ── Numeric fields ─────────────────────────────────────────────────
        $numericFields = [
            'gross_salary',
            'gsis_ee', 'gsis_ec', 'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
            'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
            'pagibig_ee', 'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity',
            'philhealth_ee',
            'withholding_tax',
            'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
            'overpayment',
            'other_deduction',
            'allowance_pera', 'allowance_rata', 'allowance_ta', 'allowance_other',
        ];

        $data = [];
        foreach ($numericFields as $field) {
            if ($request->has($field)) {
                $data[$field] = round((float) $request->input($field), 2);
            }
        }

        // ── Label fields (string, max 100 chars) ───────────────────────────
        $labelFields = [
            'label_pera',
            'label_rata',
            'label_ta',
            'label_allowance_other',
            'label_loan_lbp',
            'label_loan_dbp',
            'label_loan_cngwmpc',
            'label_loan_paracle',
            'other_deduction_label',
        ];

        foreach ($labelFields as $field) {
            if ($request->has($field)) {
                $data[$field] = substr(trim((string) $request->input($field, '')), 0, 100);
            }
        }

        $record->fill($data);

        // ── Recalculate totals ─────────────────────────────────────────────
        $totalDeductions =
              ($record->gsis_ee          ?? 0)
            + ($record->gsis_policy      ?? 0)
            + ($record->gsis_emergency   ?? 0)
            + ($record->gsis_real_estate ?? 0)
            + ($record->gsis_mpl         ?? 0)
            + ($record->gsis_mpl_lite    ?? 0)
            + ($record->gsis_gfal        ?? 0)
            + ($record->gsis_computer    ?? 0)
            + ($record->gsis_conso       ?? 0)
            + ($record->pagibig_govt     ?? 0)
            + ($record->pagibig_mpl      ?? 0)
            + ($record->pagibig_calamity ?? 0)
            + ($record->philhealth_ee    ?? 0)
            + ($record->withholding_tax  ?? 0)
            + ($record->loan_dbp         ?? 0)
            + ($record->loan_lbp         ?? 0)
            + ($record->loan_cngwmpc     ?? 0)
            + ($record->loan_paracle     ?? 0)
            + ($record->overpayment      ?? 0)
            + ($record->other_deduction  ?? 0);

        $totalAllowances =
              ($record->allowance_pera  ?? 0)
            + ($record->allowance_rata  ?? 0)
            + ($record->allowance_ta    ?? 0)
            + ($record->allowance_other ?? 0);

        $record->total_deductions = round($totalDeductions, 2);
        $record->total_allowances = round($totalAllowances, 2);
        $record->net_pay          = round(
            ($record->gross_salary ?? 0) - $totalDeductions + $totalAllowances, 2
        );
        $record->philhealth_govt  = round($record->philhealth_ee ?? 0, 2);

        $record->save();

        return response()->json(['success' => true, 'record' => $record]);
    }

    public function updateSignatory(Request $request, $period)
    {
        if (!$period instanceof PayrollPeriod) {
            $period = PayrollPeriod::findOrFail($period);
        }

        $request->validate([
            'sig_clerk_name'  => 'nullable|string|max:100',
            'sig_clerk_title' => 'nullable|string|max:100',
        ]);

        $period->update([
            'sig_clerk_name'  => strtoupper(trim($request->input('sig_clerk_name',  ''))),
            'sig_clerk_title' => trim($request->input('sig_clerk_title', '')),
        ]);

        return response()->json(['success' => true, 'period' => $period]);
    }

    public function printAll(Request $request, PayrollPeriod $period)
    {
        $query = PayrollRecord::with(['employee.position', 'period.createdBy'])
            ->where('period_id', $period->period_id)
            ->orderBy('employee_id');

        if ($empId = $request->query('emp_id')) {
            $query->where('employee_id', $empId);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            abort(404, 'No payroll records found for this period.');
        }

        $pdf = Pdf::loadView('payroll.payslip-pdf', ['records' => $records])
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled'    => true,
                'isRemoteEnabled'         => false,
                'defaultFont'             => 'helvetica',
                'dpi'                     => 96,
                'isFontSubsettingEnabled' => true,
                'enable_css_float'        => true,
                'isPhpEnabled'            => false,
            ]);

        $label = preg_replace('/[^A-Za-z0-9\-_]/', '-', $period->period_label);
        return $pdf->stream("payslips-{$label}.pdf");
    }

    public function printOne(Request $request, PayrollPeriod $period)
    {
        $empId = $request->query('emp_id');
        if (!$empId) abort(400, 'emp_id query parameter is required.');

        $record = PayrollRecord::with(['employee.position', 'period.createdBy'])
            ->where('period_id', $period->period_id)
            ->where('employee_id', $empId)
            ->firstOrFail();

        $records  = collect([$record]);
        $lastName = preg_replace('/[^A-Za-z0-9\-_]/', '-', $record->employee->last_name ?? $empId);
        $label    = preg_replace('/[^A-Za-z0-9\-_]/', '-', $period->period_label);

        $pdf = Pdf::loadView('payroll.payslip-pdf', ['records' => $records])
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled'    => true,
                'isRemoteEnabled'         => false,
                'defaultFont'             => 'helvetica',
                'dpi'                     => 96,
                'isFontSubsettingEnabled' => true,
                'enable_css_float'        => true,
                'isPhpEnabled'            => false,
            ]);

        return $pdf->stream("payslip-{$lastName}-{$label}.pdf");
    }
}