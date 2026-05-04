<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserCredential extends Authenticatable
{
    protected $table      = 'user_credentials';
    protected $primaryKey = 'credential_id';

    protected $fillable = [
        'user_id', // ── ADDED ──
        'employee_id',
        'username',
        'password_hash',
        'is_active',
    ];

    // Hide password from serialization
    protected $hidden = ['password_hash'];

    // ── Tell Laravel which column is the password ──
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // ── Relationships ──
    public function employee()
    {
        // ── UPDATED to user_id ──
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }

    public function userAccess()
    {
        // ── UPDATED to user_id ──
        return $this->hasOne(UserAccess::class, 'user_id', 'user_id')
                    ->where('is_active', 1);
    }
}