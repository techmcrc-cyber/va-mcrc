<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    protected $table = 'criteria';
    
    protected $fillable = [
        'name',
        'gender',
        'min_age',
        'max_age',
        'married',
        'vocation',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'min_age' => 'integer',
        'max_age' => 'integer',
    ];
}
