<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserCredential extends Authenticatable
{
    protected $table      = 'user_credentials';
    protected $primaryKey = 'credential_id';

    protected $fillable = [
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
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function userAccess()
    {
        return $this->hasOne(UserAccess::class, 'employee_id', 'employee_id')
                    ->where('is_active', 1);
    }
}