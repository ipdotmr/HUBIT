<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'product_id', 'order_id', 'status', 'next_due_at',
        'billing_cycle', 'recurring_amount', 'provisioner', 'provision_ref',
        'credentials', 'config',
    ];

    protected $casts = [
        'next_due_at' => 'datetime',
        'provision_ref' => 'array',
        'config' => 'array',
        'recurring_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
