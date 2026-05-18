<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserCredential extends Authenticatable
{
    protected $table      = 'user_credentials';
    protected $primaryKey = 'credential_id';

    protected $fillable = [
        'user_id',
        'employee_id',
        'username',
        'password_hash',
        'is_active',
    ];

    // Hide password from serialization
    protected $hidden = ['password_hash'];

    // ── FIX: Override auth identifier so auth()->id() returns user_id
    //         instead of credential_id. This fixes wrong employee data
    //         being loaded across ALL controllers without changing them.
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    // ── Tell Laravel which column is the password ──
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // ── Relationships ──
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }

    public function userAccess()
    {
        return $this->hasOne(UserAccess::class, 'user_id', 'user_id')
                    ->where('is_active', 1);
    }
}