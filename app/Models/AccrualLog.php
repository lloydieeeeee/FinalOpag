<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccrualLog extends Model
{
    public $timestamps    = false;
    protected $table      = 'accrual_log';
    protected $primaryKey = 'accrual_log_id';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'credit_balance_id',
        'accrual_date',
        'days_accrued',
        'remarks',
    ];
}