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
        'is_active',
    ];

    protected $casts = [
        'is_accrual_based' => 'boolean',
        'is_active'        => 'boolean',
        'accrual_rate'     => 'decimal:2',
        'max_days'         => 'decimal:2',
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