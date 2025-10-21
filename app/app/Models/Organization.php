<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}
