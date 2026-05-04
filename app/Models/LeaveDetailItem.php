<?php
// app/Models/LeaveDetailItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDetailItem extends Model
{
    protected $table    = 'leave_detail_items';
    protected $fillable = ['group_id', 'label', 'has_text_input', 'sort_order'];

    protected $casts = [
        'has_text_input' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(LeaveDetailGroup::class, 'group_id');
    }
}