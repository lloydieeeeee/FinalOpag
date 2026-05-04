<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    protected $table      = 'payroll_record';
    protected $primaryKey = 'payroll_id';

    protected $fillable = [
        'period_id', 'employee_id', 'designation',
        'gross_salary',
        'gsis_ee', 'gsis_govt', 'gsis_ec',
        'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
        'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
        'pagibig_ee', 'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity',
        'philhealth_ee', 'philhealth_govt',
        'withholding_tax',
        'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
        'label_loan_lbp', 'label_loan_dbp', 'label_loan_cngwmpc', 'label_loan_paracle',
        'overpayment',
        'other_deduction', 'other_deduction_label',
        'allowance_pera', 'allowance_rata', 'allowance_ta', 'allowance_other',
        'label_pera', 'label_rata', 'label_ta', 'label_allowance_other',
        'total_deductions', 'total_allowances', 'net_pay',
        'dynamic_deductions',
        'remarks',
    ];

    protected $casts = [
        'gross_salary'       => 'float',
        'gsis_ee'            => 'float',
        'gsis_govt'          => 'float',
        'gsis_ec'            => 'float',
        'gsis_policy'        => 'float',
        'gsis_emergency'     => 'float',
        'gsis_real_estate'   => 'float',
        'gsis_mpl'           => 'float',
        'gsis_mpl_lite'      => 'float',
        'gsis_gfal'          => 'float',
        'gsis_computer'      => 'float',
        'gsis_conso'         => 'float',
        'pagibig_ee'         => 'float',
        'pagibig_govt'       => 'float',
        'pagibig_mpl'        => 'float',
        'pagibig_calamity'   => 'float',
        'philhealth_ee'      => 'float',
        'philhealth_govt'    => 'float',
        'withholding_tax'    => 'float',
        'loan_dbp'           => 'float',
        'loan_lbp'           => 'float',
        'loan_cngwmpc'       => 'float',
        'loan_paracle'       => 'float',
        'overpayment'        => 'float',
        'other_deduction'    => 'float',
        'allowance_pera'     => 'float',
        'allowance_rata'     => 'float',
        'allowance_ta'       => 'float',
        'allowance_other'    => 'float',
        'total_deductions'   => 'float',
        'total_allowances'   => 'float',
        'net_pay'            => 'float',
        'dynamic_deductions' => 'array',
    ];

    public function employee(): BelongsTo
    {
        // ADDED withDefault() to prevent "Attempt to read property on null"
        // if an employee ID was changed or deleted.
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id')->withDefault([
            'first_name' => 'Unknown',
            'last_name' => 'Employee',
            'extension_name' => null
        ]);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id', 'period_id');
    }

    public static function computeFromSalary(float $salary, array $overrides = []): array
    {
        return (new \App\Http\Controllers\PayrollController)->computeFromSalary($salary, $overrides);
    }

    public function recomputeTotals(?\Illuminate\Support\Collection $deductionConfig = null): void
    {
        $totalDeductions =
              ($this->gsis_ee           ?? 0)
            + ($this->gsis_policy       ?? 0)
            + ($this->gsis_emergency    ?? 0)
            + ($this->gsis_real_estate  ?? 0)
            + ($this->gsis_mpl          ?? 0)
            + ($this->gsis_mpl_lite     ?? 0)
            + ($this->gsis_gfal         ?? 0)
            + ($this->gsis_computer     ?? 0)
            + ($this->gsis_conso        ?? 0)
            + ($this->pagibig_govt      ?? 0)
            + ($this->pagibig_mpl       ?? 0)
            + ($this->pagibig_calamity  ?? 0)
            + ($this->philhealth_ee     ?? 0)
            + ($this->withholding_tax   ?? 0)
            + ($this->loan_dbp          ?? 0)
            + ($this->loan_lbp          ?? 0)
            + ($this->loan_cngwmpc      ?? 0)
            + ($this->loan_paracle      ?? 0)
            + ($this->overpayment       ?? 0)
            + ($this->other_deduction   ?? 0);

        $totalAllowances =
              ($this->allowance_pera  ?? 0)
            + ($this->allowance_rata  ?? 0)
            + ($this->allowance_ta    ?? 0)
            + ($this->allowance_other ?? 0);

        $dynamic = $this->dynamic_deductions ?? [];

        if (!empty($dynamic)) {
            if ($deductionConfig === null) {
                $deductionConfig = PayrollDeduction::active()->ordered()->get()->keyBy('id');
            }

            foreach ($dynamic as $dedId => $amount) {
                $amount = (float) $amount;
                $ded = $deductionConfig->get((int) $dedId);

                if ($ded === null) {
                    $totalDeductions += $amount;
                    continue;
                }

                if ($ded->isAllowance()) {
                    $totalAllowances += $amount;
                } else {
                    $totalDeductions += $amount;
                }
            }
        }

        $this->total_deductions = round($totalDeductions, 2);
        $this->total_allowances = round($totalAllowances, 2);
        $this->net_pay          = round(($this->gross_salary ?? 0) - $totalDeductions + $totalAllowances, 2);
        $this->philhealth_govt  = round($this->philhealth_ee ?? 0, 2);
    }

    public function getDynamicDeduction(int $deductionId): float
    {
        return (float) ($this->dynamic_deductions[$deductionId] ?? 0);
    }

    public function setDynamicDeduction(int $deductionId, float $amount): void
    {
        $current = $this->dynamic_deductions ?? [];
        $current[$deductionId] = round($amount, 2);
        $this->dynamic_deductions = $current;
    }
}