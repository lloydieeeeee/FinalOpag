<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $table      = 'user_access';
    protected $primaryKey = 'access_id';

    protected $fillable = [
        'user_id', // ── ADDED ──
        'employee_id',
        'user_access',
        'is_active',
    ];

    public function employee()
    {
        // ── UPDATED to user_id ──
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }
}