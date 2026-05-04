<?php
// ══════════════════════════════════════════════════
// app/Models/HalfDay.php
// ══════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalfDay extends Model
{
    protected $table      = 'half_day';
    protected $primaryKey = 'half_day_id';
    public    $timestamps = false; // table uses created_at/updated_at but not Laravel default

    protected $fillable = [
        'employee_id',
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


// ══════════════════════════════════════════════════
// Add these to routes/web.php inside your auth middleware group
// ══════════════════════════════════════════════════

// Route::prefix('application')->middleware('auth')->group(function () {
//
//     // Half Day Certification
//     Route::get('/halfday',          [HalfDayController::class, 'index'])->name('halfday.index');
//     Route::post('/halfday',         [HalfDayController::class, 'store'])->name('halfday.store');
//     Route::post('/halfday/{id}/cancel', [HalfDayController::class, 'cancel'])->name('halfday.cancel');
//
// });

