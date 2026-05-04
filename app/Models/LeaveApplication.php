<?php
// app/Models/LeaveApplication.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    protected $table      = 'leave_application';  // NOT pluralized
    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'credit_balance_id',
        'details_of_leave',
        'application_date',
        'start_date',
        'end_date',
        'no_of_days',
        'reason',
        'is_monetization',
        'commutation',
        'status',
        'approved_by',
        'approved_at',
        'reject_reason',
        'leave_dates',
    ];

    protected $casts = [
        'application_date' => 'date',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'approved_at'      => 'datetime',
        'is_monetization'  => 'boolean',
        'no_of_days'       => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'leave_type_id');
    }

    public function creditBalance()
    {
        return $this->belongsTo(LeaveCreditBalance::class, 'credit_balance_id', 'credit_balance_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'employee_id');
    }
}