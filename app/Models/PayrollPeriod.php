<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PayrollPeriod extends Model
{
    protected $table      = 'payroll_period';
    protected $primaryKey = 'period_id';

    protected $fillable = [
        'period_label', 'month', 'year', 'status', 'created_by',
        'sig_clerk_name', 'sig_clerk_title',
    ];

    protected $casts = [
        'year'  => 'integer',
        'month' => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function records(): HasMany
    {
        return $this->hasMany(PayrollRecord::class, 'period_id', 'period_id');
    }

    /**
     * The employee who created this period.
     * `created_by` stores an employee_id (NOT a Laravel users.id).
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by', 'employee_id');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Static helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return (or create) the PayrollPeriod for the current calendar month.
     * Used as a fallback when no FINALIZED period exists.
     */
    public static function current(): static
    {
        $now = Carbon::now();

        return static::firstOrCreate(
            [
                'month' => $now->month,
                'year'  => $now->year,
            ],
            [
                'period_label' => $now->format('F Y'),
                'status'       => 'DRAFT',
                'created_by'   => null,
            ]
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────────────────────────────────

    /** e.g. "April" */
    public function getMonthNameAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────────────────────────────────

    /** Order newest-first. Backtick-quoted because year/month are reserved. */
    public function scopeNewestFirst($query)
    {
        return $query->orderByRaw('`year` DESC, `month` DESC');
    }
}