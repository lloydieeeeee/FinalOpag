<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table      = 'leave_type';
    protected $primaryKey = 'leave_type_id';

    protected $fillable = [
        'type_name',
        'type_code',
        'legal_reference',
        'is_accrual_based',
        'accrual_rate',
        'max_days',
        'notice_days',       // NEW: Days required prior to filing
        'allow_past_filing', // NEW: Override to allow late/emergency filing
        'is_active',
    ];

    protected $casts = [
        'is_accrual_based'  => 'boolean',
        'allow_past_filing' => 'boolean',
        'is_active'         => 'boolean',
        'accrual_rate'      => 'decimal:2',
        'max_days'          => 'decimal:2',
        'notice_days'       => 'integer',
    ];

    public function creditBalances()
    {
        return $this->hasMany(LeaveCreditBalance::class, 'leave_type_id', 'leave_type_id');
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'leave_type_id', 'leave_type_id');
    }
}