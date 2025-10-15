<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'base',
        'quote',
        'rate',
        'fetched_at',
        'meta',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'fetched_at' => 'datetime',
        'meta' => 'array',
    ];

    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base', 'code');
    }

    public function quoteCurrency()
    {
        return $this->belongsTo(Currency::class, 'quote', 'code');
    }
}
