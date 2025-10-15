<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $fillable = [
        'type',
        'name',
        'currency',
        'account_number',
        'bank_name',
        'swift_bic',
        'instructions',
        'active',
        'created_by',
    ];

    protected $casts = [
        'instructions' => 'array',
        'active' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getDisplayInstructions(string $lang = 'en'): array
    {
        $instructions = $this->instructions ?? [];

        return [
            'name' => $this->name,
            'type' => $this->type,
            'currency' => $this->currency,
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'swift_bic' => $this->swift_bic,
            'instructions' => $instructions[$lang] ?? $instructions['en'] ?? '',
        ];
    }
}
