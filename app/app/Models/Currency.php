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
        'decimals',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'decimals' => 'integer',
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
