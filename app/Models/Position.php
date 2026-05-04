<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table      = 'position'; // singular — matches your new DB
    protected $primaryKey = 'position_id';

    protected $fillable = [
        'position_name',
        'position_code',
        'is_active',
    ];
}