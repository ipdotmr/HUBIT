<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sort_order',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'group_id');
    }
}
