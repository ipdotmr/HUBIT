<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Temporary stub model for Domain
 * Full implementation will be completed in Stage 3, Milestone B
 */
class Domain extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];
}
