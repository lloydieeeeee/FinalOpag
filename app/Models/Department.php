<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table      = 'department'; // singular
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_name',
        'is_active',
    ];
}