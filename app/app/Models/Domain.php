<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'order_id',
        'domain',
        'registrar',
        'status',
        'registered_at',
        'expires_at',
        'auto_renew',
        'privacy_enabled',
        'lock_enabled',
        'nameservers',
        'epp_code',
        'whois_data',
        'registrar_meta',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'expires_at' => 'date',
        'auto_renew' => 'boolean',
        'privacy_enabled' => 'boolean',
        'lock_enabled' => 'boolean',
        'nameservers' => 'array',
        'whois_data' => 'array',
        'registrar_meta' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function dnsRecords(): HasMany
    {
        return $this->hasMany(DomainDnsRecord::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(DomainRenewal::class);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return $this->expires_at->diffInDays(now()) <= $days && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
