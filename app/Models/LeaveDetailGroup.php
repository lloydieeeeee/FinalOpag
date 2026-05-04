<?php
// app/Models/LeaveDetailGroup.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDetailGroup extends Model
{
    protected $table    = 'leave_detail_groups';
    protected $fillable = ['group_name', 'color', 'sort_order'];

    public function items()
    {
        return $this->hasMany(LeaveDetailItem::class, 'group_id')
                    ->orderBy('sort_order');
    }
}