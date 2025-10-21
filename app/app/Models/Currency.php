<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'prefix',
        'suffix',
        'format',
        'decimals',
        'exchange_rate_to_usd',
        'is_default',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'is_default' => 'boolean',
        'decimals' => 'integer',
        'exchange_rate_to_usd' => 'decimal:6',
    ];

    public function exchangeRatesAsBase()
    {
        return $this->hasMany(ExchangeRate::class, 'base', 'code');
    }

    public function exchangeRatesAsQuote()
    {
        return $this->hasMany(ExchangeRate::class, 'quote', 'code');
    }
}
