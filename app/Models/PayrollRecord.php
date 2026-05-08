<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    protected $table      = 'payroll_record';
    protected $primaryKey = 'payroll_id';

    protected $fillable = [
        'period_id',
        'user_id',          // sole FK — references employee.user_id
        'designation',
        'gross_salary',
        'gsis_ee', 'gsis_govt', 'gsis_ec',
        'gsis_policy', 'gsis_emergency', 'gsis_real_estate',
        'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_conso',
        'pagibig_ee', 'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity',
        'philhealth_ee', 'philhealth_govt',
        'withholding_tax',
        'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
        'overpayment', 'overpayment_label',
        'other_deduction', 'other_deduction_label',
        'allowance_pera', 'allowance_rata', 'allowance_ta', 'allowance_other',
        'total_deductions', 'total_allowances', 'net_pay',
        'dynamic_deductions',
        'remarks',

        // Label fields
        'label_pera', 'label_rata', 'label_ta', 'label_allowance_other',
        'label_withholding_tax', 'label_gsis_ee', 'label_gsis_ec', 'label_gsis_policy',
        'label_gsis_emergency', 'label_gsis_real_estate', 'label_gsis_mpl', 'label_gsis_mpl_lite',
        'label_gsis_gfal', 'label_gsis_computer', 'label_gsis_conso', 'label_pagibig_govt',
        'label_pagibig_mpl', 'label_pagibig_calamity', 'label_philhealth_ee',
        'label_loan_lbp', 'label_loan_dbp', 'label_loan_cngwmpc', 'label_loan_paracle',
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
        // 'array' cast handles json_encode on save and json_decode on load.
        // Do NOT manually json_encode() this field anywhere — it will double-encode
        // and break every foreach() that reads it back.
        'dynamic_deductions' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Safe accessor: guarantees dynamic_deductions is ALWAYS a plain PHP array.
    // Prevents "foreach() argument must be of type array|object, string given"
    // even if stale double-encoded JSON exists in the database.
    // -------------------------------------------------------------------------
    public function getDynamicDeductionsAttribute(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            // Handle double-encoded strings (e.g. "\"[...]\"")
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id', 'user_id')->withDefault([
            'first_name'     => 'Unknown',
            'last_name'      => 'Employee',
            'extension_name' => null,
        ]);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id', 'period_id');
    }

    // -------------------------------------------------------------------------
    // Static helper
    // -------------------------------------------------------------------------

    public static function computeFromSalary(float $salary, array $overrides = []): array
    {
        return (new \App\Http\Controllers\PayrollController)->computeFromSalary($salary, $overrides);
    }

    // -------------------------------------------------------------------------
    // Recompute totals (including dynamic deductions)
    // -------------------------------------------------------------------------

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

        // Safe: accessor above guarantees this is always an array
        $dynamic = $this->dynamic_deductions;

        if (!empty($dynamic)) {
            if ($deductionConfig === null) {
                $deductionConfig = PayrollDeduction::active()->ordered()->get()->keyBy('id');
            }

            foreach ($dynamic as $dedId => $amount) {
                $amount = (float) $amount;
                $ded    = $deductionConfig->get((int) $dedId);

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

    // -------------------------------------------------------------------------
    // Dynamic deduction helpers
    // -------------------------------------------------------------------------

    public function getDynamicDeduction(int $deductionId): float
    {
        return (float) ($this->dynamic_deductions[$deductionId] ?? 0);
    }

    public function setDynamicDeduction(int $deductionId, float $amount): void
    {
        $current               = $this->dynamic_deductions; // always an array via accessor
        $current[$deductionId] = round($amount, 2);
        $this->dynamic_deductions = $current;
    }
}