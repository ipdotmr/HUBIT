<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportDepartment extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'name_fr',
        'description_en',
        'description_ar',
        'description_fr',
        'email',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getName($locale = 'en')
    {
        $field = "name_{$locale}";
        return $this->$field ?? $this->name_en;
    }

    public function getDescription($locale = 'en')
    {
        $field = "description_{$locale}";
        return $this->$field ?? $this->description_en;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }
}
