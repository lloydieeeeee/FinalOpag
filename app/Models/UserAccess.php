<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $table      = 'user_access';
    protected $primaryKey = 'access_id';

    protected $fillable = [
        'employee_id',
        'user_access',
        'is_active',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}