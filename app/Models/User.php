<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user_credential'; // your table
    protected $primaryKey = 'credential_id';

    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'username',
        'password_hash',
        'is_active'
    ];

    protected $hidden = [
        'password_hash'
    ];

    /**
     * Tell Laravel which column is the password
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Optional: relationship to employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}