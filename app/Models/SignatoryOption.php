<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignatoryOption extends Model
{
    protected $table = 'signatory_options';

    protected $fillable = [
        'label',
        'full_name',
        'title',
        'sort_order',
    ];
}