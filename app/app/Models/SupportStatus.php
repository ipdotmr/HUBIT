<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportStatus extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'name_fr',
        'color',
        'is_active',
        'is_closed',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getName($locale = 'en')
    {
        $field = "name_{$locale}";
        return $this->$field ?? $this->name_en;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
