<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group', 'name', 'slug', 'description', 'is_active', 'billing_cycles',
        'base_price', 'config_options', 'stock', 'provisioner', 'provisioner_config',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'billing_cycles' => 'array',
        'config_options' => 'array',
        'provisioner_config' => 'array',
        'base_price' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
