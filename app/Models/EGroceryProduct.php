<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EGroceryProduct extends Model
{
    protected $table = 'e_grocery_products';

    protected $fillable = [
        'external_sku',
        'name',
        'category',
        'price',
        'stock',
        'status',
        'external_image_id',
        'source_updated_at',
        'payload',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'source_updated_at' => 'datetime',
        'payload' => 'array',
    ];
}

