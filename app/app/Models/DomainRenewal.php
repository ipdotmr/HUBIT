<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'years',
        'invoice_id',
        'status',
        'old_expiry',
        'new_expiry',
    ];

    protected $casts = [
        'years' => 'integer',
        'old_expiry' => 'date',
        'new_expiry' => 'date',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
