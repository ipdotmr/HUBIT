<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Temporary stub model for DomainOrder
 * Full implementation will be completed in Stage 3, Milestone B
 */
class DomainOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'years' => 'integer',
        'price' => 'decimal:2',
    ];
}
