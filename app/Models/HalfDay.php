<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalfDay extends Model
{
    protected $table      = 'half_day';
    protected $primaryKey = 'half_day_id';
    public    $timestamps = false; 

    protected $fillable = [
        'user_id', // Changed from employee_id
        'leave_type_id',
        'credit_balance_id',
        'application_date',
        'date_of_absence',
        'time_period',
        'reason',
        'status',
        'approved_by',
        'approved_date',
    ];

    protected $casts = [
        'application_date' => 'date',
        'date_of_absence'  => 'date',
        'approved_date'    => 'date',
    ];

    public function employee()
    {
        // Directly maps to Employee using user_id
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
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
        // Approver is also mapped to Employee via user_id
        return $this->belongsTo(Employee::class, 'approved_by', 'user_id');
    }
}