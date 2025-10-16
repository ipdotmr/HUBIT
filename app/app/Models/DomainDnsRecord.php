<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainDnsRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'type',
        'host',
        'value',
        'priority',
        'ttl',
        'meta',
    ];

    protected $casts = [
        'priority' => 'integer',
        'ttl' => 'integer',
        'meta' => 'array',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
