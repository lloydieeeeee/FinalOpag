<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * PayrollDeduction Model
 *
 * Table columns (verified from DB dump):
 *   id, parent_id (nullable FK → self), name, type ('Fixed'|'Not Fixed'),
 *   rate (display label), rate_value (decimal), rate_type ('percent'|'flat'),
 *   limit_amount (nullable decimal), status (varchar), is_active (tinyint),
 *   is_deducted (tinyint), entry_kind ('deduction'|'addition'),
 *   sort_order (int), created_at, updated_at
 */
class PayrollDeduction extends Model
{
    protected $table = 'payroll_deductions';

    protected $fillable = [
        'name', 'type', 'rate', 'rate_value', 'rate_type',
        'limit_amount', 'status', 'is_active', 'sort_order',
        'parent_id', 'is_deducted', 'entry_kind',
    ];

    protected $casts = [
        'rate_value'   => 'float',
        'limit_amount' => 'float',
        'is_active'    => 'boolean',
        'is_deducted'  => 'boolean',
        'sort_order'   => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function parent()
    {
        return $this->belongsTo(PayrollDeduction::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PayrollDeduction::class, 'parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('id');
    }

    // ── Special-case IDs ─────────────────────────────────────────────────────
    //
    // These rows are stored as "Not Fixed" in the DB (historic data entry
    // mistake) but must behave as fixed auto-computed amounts.
    //
    //   13 = ECF (Employee Compensation Fund) → flat ₱100
    //
    // NOTE: Pag-IBIG (IDs 22 & 23) are NO LONGER listed here.
    //       They use tiered percentage rates per HDMF 2026 rules.
    //
    private const SPECIAL_FIXED_AMOUNTS = [
        13 => 100.00,   // ECF
    ];

    // ── Pag-IBIG 2025-2026 configuration ─────────────────────────────────────
    //
    // Per HDMF updated rules (effective 2025):
    //   Monthly compensation ₱1,000 – ₱1,499.99 → EE 1%,  ER 2%
    //   Monthly compensation ₱1,500 and above    → EE 2%,  ER 2%
    //   Maximum monthly compensation used         → ₱10,000
    //
    // Max contribution:
    //   EE = ₱10,000 × 2% = ₱200 per month
    //   ER = ₱10,000 × 2% = ₱200 per month
    //
    private const PAGIBIG_EE_ID   = 22;
    private const PAGIBIG_ER_ID   = 23;
    private const PAGIBIG_CEILING = 10000.00;

    // ── ID → hard payroll_record column mapping ───────────────────────────────
    //
    // Only deductions that have a dedicated column in payroll_record are listed.
    // All others → null (they go into dynamic_deductions JSON).
    //
    private const ID_TO_COLUMN = [
        // GSIS
        11 => 'gsis_ee',           // Life Retirement Insurance – Personal Share  9%
        12 => 'gsis_govt',         // Life Retirement Insurance – Government Share 12%
        13 => 'gsis_ec',           // ECF
        15 => 'gsis_policy',       // Policy Loan
        16 => 'gsis_emergency',    // Emergency Loan
        17 => 'gsis_real_estate',  // Real Estate Loan
        18 => 'gsis_computer',     // Computer Loan
        19 => 'gsis_gfal',         // GFAL
        20 => 'gsis_mpl',          // MPL
        21 => 'gsis_mpl_lite',     // MPL Lite
        14 => 'gsis_conso',        // Conso Loan

        // Pag-IBIG
        22 => 'pagibig_ee',        // Employee Share (tiered %)
        23 => 'pagibig_govt',      // Employer Share (flat 2%)
        24 => 'pagibig_mpl',       // MPL
        25 => 'pagibig_calamity',  // Calamity Loan

        // PhilHealth
        28 => 'philhealth_ee',     // Personal Share
        29 => 'philhealth_govt',   // Government Share

        // Withholding Tax
        7  => 'withholding_tax',

        // Others (hard columns)
        30 => 'loan_dbp',
        31 => 'loan_lbp',

        // Allowances
        1  => 'allowance_pera',    // PERA
        2  => 'allowance_rata',    // RA (Representation Allowance)
        3  => 'allowance_ta',      // TA (Transportation Allowance)
    ];

    /**
     * Return the payroll_record hard-column name for this deduction, or null
     * if it should go into dynamic_deductions.
     */
    public function resolveColumn(): ?string
    {
        return self::ID_TO_COLUMN[$this->id] ?? null;
    }

    /**
     * Should this deduction be auto-computed (true) or left at zero unless
     * the user provides a value (false)?
     */
    public function isFixed(): bool
    {
        if (isset(self::SPECIAL_FIXED_AMOUNTS[$this->id])) {
            return true;
        }
        // Pag-IBIG is always computed (tiered)
        if ($this->id === self::PAGIBIG_EE_ID || $this->id === self::PAGIBIG_ER_ID) {
            return true;
        }
        return $this->type === 'Fixed';
    }

    /**
     * Is this an allowance (addition to net) rather than a deduction?
     */
    public function isAllowance(): bool
    {
        return $this->entry_kind === 'addition';
    }

    // ── Pag-IBIG tiered computation (2025-2026) ───────────────────────────────

    /**
     * Compute Pag-IBIG contribution using updated HDMF tiered rules.
     *
     * Bracket table:
     *   ₱1,000 – ₱1,499.99 → EE 1%,  ER 2%
     *   ₱1,500 and above    → EE 2%,  ER 2%
     *
     * The maximum monthly compensation used for computation is ₱10,000,
     * so the hard cap per month is:
     *   EE max = ₱10,000 × 2% = ₱200
     *   ER max = ₱10,000 × 2% = ₱200
     *
     * @param  float  $gross       Employee's monthly basic salary
     * @param  bool   $isEmployer  True when computing the employer/govt share (ID 23)
     * @return float
     */
    public static function computePagibig(float $gross, bool $isEmployer = false): float
    {
        // Apply the monthly compensation ceiling
        $base = min($gross, self::PAGIBIG_CEILING);

        // Determine employee rate based on salary bracket
        if ($gross < 1500.00) {
            // Bracket 1: below ₱1,500 (includes < ₱1,000 — still assessed at 1%/2%)
            $eeRate = 0.01;
        } else {
            // Bracket 2: ₱1,500 and above
            $eeRate = 0.02;
        }

        // Employer rate is always 2% regardless of bracket
        $erRate = 0.02;

        $rate   = $isEmployer ? $erRate : $eeRate;
        return round($base * $rate, 2);
    }

    /**
     * Compute the deduction/allowance amount for the given gross salary.
     */
    public function compute(float $gross): float
    {
        // ── Pag-IBIG tiered (2025-2026 HDMF rules) ───────────────────────────
        if ($this->id === self::PAGIBIG_EE_ID) {
            return self::computePagibig($gross, false);
        }
        if ($this->id === self::PAGIBIG_ER_ID) {
            return self::computePagibig($gross, true);
        }

        // ── Special-case IDs with hard flat amounts (e.g. ECF ₱100) ──────────
        if (isset(self::SPECIAL_FIXED_AMOUNTS[$this->id])) {
            // Allow a DB override: if rate_value is set, honour it
            if ($this->rate_value > 0) {
                $amount = $this->rate_type === 'percent'
                    ? round($gross * $this->rate_value, 2)
                    : round($this->rate_value, 2);
            } else {
                $amount = self::SPECIAL_FIXED_AMOUNTS[$this->id];
            }
            return $amount;
        }

        // ── Standard percent or flat ──────────────────────────────────────────
        if ($this->rate_type === 'percent') {
            $amount = round($gross * $this->rate_value, 2);
        } else {
            $amount = round($this->rate_value, 2);
        }

        if ($this->limit_amount !== null && $this->limit_amount > 0) {
            $amount = min($amount, $this->limit_amount);
        }

        return $amount;
    }

    // ── Cache helpers ─────────────────────────────────────────────────────────

    public static function flushCache(): void
    {
        Cache::forget('payroll_deductions_active');
    }

    protected static function booted(): void
    {
        static::saved(fn() => static::flushCache());
        static::deleted(fn() => static::flushCache());
    }

    /**
     * Load all active deductions, keyed by lowercase name.
     * Cached for 30 minutes.
     */
    public static function loadActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('payroll_deductions_active', 1800, function () {
            return static::active()->ordered()->get()->keyBy(fn($d) => strtolower($d->name));
        });
    }

    /**
     * Build the JS config array used by Blade views to seed frontend computation.
     * Queries by stable IDs, not names.
     *
     * Pag-IBIG is exported as 'tiered' type so the frontend knows to call
     * the bracket function instead of a simple percent/flat multiply.
     */
    public static function buildJsConfig(): array
    {
        $byId = static::active()->ordered()->get()->keyBy('id');

        $get = function (int $id) use ($byId): ?PayrollDeduction {
            return $byId->get($id);
        };

        $cfg = function (?PayrollDeduction $ded, string $defaultType, float $defaultValue, ?float $defaultLimit = null) {
            if (!$ded) {
                return ['type' => $defaultType, 'value' => $defaultValue, 'limit' => $defaultLimit];
            }
            if (isset(self::SPECIAL_FIXED_AMOUNTS[$ded->id]) && $ded->rate_value == 0) {
                return [
                    'type'  => 'flat',
                    'value' => self::SPECIAL_FIXED_AMOUNTS[$ded->id],
                    'limit' => null,
                ];
            }
            return [
                'type'  => $ded->rate_type,
                'value' => $ded->rate_value,
                'limit' => $ded->limit_amount,
            ];
        };

        $gsisEe   = $cfg($get(11), 'percent', 0.09);
        $gsisGovt = $cfg($get(12), 'percent', 0.12);
        $gsisEc   = $cfg($get(13), 'flat', 100.0);
        $phicEe   = $cfg($get(28), 'percent', 0.025);
        $phicGovt = $cfg($get(29), 'percent', 0.025);
        $pera     = $cfg($get(1),  'flat', 2000.0);
        $ra       = $cfg($get(2),  'flat', 9500.0);
        $ta       = $cfg($get(3),  'flat', 9500.0);

        return [
            // GSIS
            'gsisEeType'      => $gsisEe['type'],
            'gsisEeValue'     => $gsisEe['value'],
            'gsisEeLimit'     => $gsisEe['limit'],
            'gsisGovtType'    => $gsisGovt['type'],
            'gsisGovtValue'   => $gsisGovt['value'],
            'gsisGovtLimit'   => $gsisGovt['limit'],
            'gsisEcType'      => $gsisEc['type'],
            'gsisEcValue'     => $gsisEc['value'],

            // Pag-IBIG — tiered 2025-2026 HDMF rules
            // Frontend must call computePagibig(gross, isEmployer) instead of
            // a simple multiply. Ceiling is ₱10,000.
            // Brackets: gross < ₱1,500 → EE 1%, ER 2%
            //           gross ≥ ₱1,500 → EE 2%, ER 2%
            'pagibigType'       => 'tiered',
            'pagibigCeiling'    => self::PAGIBIG_CEILING,   // 10000.00
            'pagibigEeRateLow'  => 0.01,   // EE rate when gross < ₱1,500
            'pagibigEeRateHigh' => 0.02,   // EE rate when gross ≥ ₱1,500
            'pagibigErRate'     => 0.02,   // ER rate (flat across all brackets)

            // PhilHealth
            'phicEeType'      => $phicEe['type'],
            'phicEeValue'     => $phicEe['value'],
            'phicEeLimit'     => $phicEe['limit'],
            'phicGovtType'    => $phicGovt['type'],
            'phicGovtValue'   => $phicGovt['value'],
            'phicGovtLimit'   => $phicGovt['limit'],

            // Allowances
            'peraType'        => $pera['type'],
            'peraValue'       => $pera['value'],
            'raType'          => $ra['type'],
            'raValue'         => $ra['value'],
            'taType'          => $ta['type'],
            'taValue'         => $ta['value'],
        ];
    }
}