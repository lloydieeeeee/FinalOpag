<?php
// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table      = 'employee';
    protected $primaryKey = 'employee_id';
    public    $incrementing = false;   // employee_id is not auto-increment
    protected $keyType    = 'int';

    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'birthday',
        'contact_number',
        'address',
        'hire_date',
        'department_id',
        'position_id',
        'salary',
        'is_active',
        'pagibig_id',
        'gsis_id',
        'philhealth_id',
        'tin',
    ];

    // ── Relationships ──────────────────────────────

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function credential()
    {
        return $this->hasOne(UserCredential::class, 'employee_id', 'employee_id');
    }

    public function access()
    {
        return $this->hasOne(UserAccess::class, 'employee_id', 'employee_id');
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'employee_id', 'employee_id');
    }

    public function creditBalances()
    {
        return $this->hasMany(LeaveCreditBalance::class, 'employee_id', 'employee_id');
    }

    // ── ADDED: Half-day applications ──────────────
    public function halfDays()
    {
        return $this->hasMany(HalfDay::class, 'employee_id', 'employee_id');
    }

    // ── ADDED: Payroll records ─────────────────────
    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class, 'employee_id', 'employee_id');
    }

    // ── Accessors ──────────────────────────────────

    /**
     * Format employee_id as XXX-XXXX (e.g. 160624 → 016-0624)
     */
    public function getFormattedEmployeeIdAttribute(): string
    {
        $id = str_pad((string) $this->employee_id, 7, '0', STR_PAD_LEFT);
        return substr($id, 0, 3) . '-' . substr($id, 3);
    }

    /**
     * Full name: First M. Last Ext
     */
    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name ? substr($this->middle_name, 0, 1) . '.' : null,
            $this->last_name,
            $this->extension_name,
        ])));
    }

    /**
     * Full name formal: LAST, First M. Ext
     */
    public function getFormalNameAttribute(): string
    {
        $mid = $this->middle_name ? ' ' . substr($this->middle_name, 0, 1) . '.' : '';
        $ext = $this->extension_name ? ' ' . $this->extension_name : '';
        return strtoupper($this->last_name) . ', ' . $this->first_name . $mid . $ext;
    }

    // ── Helpers ────────────────────────────────────

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}