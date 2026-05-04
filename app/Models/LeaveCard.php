<?php
// ============================================================
// app/Models/LeaveCard.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCard extends Model
{
    protected $table      = 'leave_card';
    protected $primaryKey = 'leave_card_id';
    public    $timestamps = true;

    protected $fillable = [
        'employee_id', 'year', 'opening_vl', 'opening_sl', 'created_by',
    ];

    protected $casts = [
        'opening_vl' => 'decimal:3',
        'opening_sl' => 'decimal:3',
        'year'       => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function entries()
    {
        return $this->hasMany(LeaveCardEntry::class, 'leave_card_id')->orderBy('entry_order');
    }
}


// ============================================================
// app/Models/LeaveCardEntry.php  (separate file in production)
// ============================================================
namespace App\Models;

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class LeaveCardEntry extends Model
{
    protected $table = 'leave_card_entry';
    protected $primaryKey = 'entry_id';
 
    protected $fillable = [
        'leave_card_id',
        'entry_order',
        'month',
        'date_particulars',
        'earned_vl',
        'earned_sl',
        'taken_vl',
        'taken_sl',
        'leave_wop',
        'tardy_undertime',
        'balance_vl',
        'balance_sl',
        'remarks',
        'status',
        'leave_application_id',
        'half_day_id',          // ← added for half-day tombstone tracking
        'is_manual',
        'created_by',
    ];
 
    protected $casts = [
        'earned_vl'       => 'decimal:3',
        'earned_sl'       => 'decimal:3',
        'taken_vl'        => 'decimal:3',
        'taken_sl'        => 'decimal:3',
        'leave_wop'       => 'decimal:3',
        'tardy_undertime' => 'decimal:3',
        'balance_vl'      => 'decimal:3',
        'balance_sl'      => 'decimal:3',
        'is_manual'       => 'boolean',
    ];
 
    /* ── Relationships ── */
    public function leaveCard()
    {
        return $this->belongsTo(LeaveCard::class, 'leave_card_id');
    }
 
    public function leaveApplication()
    {
        return $this->belongsTo(LeaveApplication::class, 'leave_application_id', 'leave_id');
    }
 
    /* Half-day relationship — uses DB facade since there may be no HalfDay model yet */
    public function halfDay()
    {
        return $this->belongsTo(\App\Models\HalfDay::class, 'half_day_id', 'half_day_id');
    }
}