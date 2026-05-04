<?php
// app/Models/LeaveCreditBalance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCreditBalance extends Model
{
    protected $table      = 'leave_credit_balance';  // NOT pluralized
    protected $primaryKey = 'credit_balance_id';

    // app/Models/LeaveCreditBalance.php
// app/Models/LeaveCreditBalance.php
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'total_accrued',
        'total_used',
        'remaining_balance',
        'vacation_balance',
        'sick_balance',
];

    protected $casts = [
        'total_accrued'     => 'decimal:2',
        'total_used'        => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'year'              => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'leave_type_id');
    }
}