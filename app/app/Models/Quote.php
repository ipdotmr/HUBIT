<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'quote_number',
        'subject',
        'notes',
        'status',
        'subtotal',
        'tax',
        'total',
        'currency',
        'valid_until',
        'sent_at',
        'accepted_at',
        'declined_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'sent' => 'bg-info',
            'accepted' => 'bg-success',
            'declined' => 'bg-danger',
            'expired' => 'bg-warning',
            default => 'bg-secondary',
        };
    }
}
