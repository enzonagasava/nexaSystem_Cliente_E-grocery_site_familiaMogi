<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EGroceryAd extends Model
{
    protected $table = 'e_grocery_ads';

    protected $fillable = [
        'external_ad_id',
        'title',
        'description',
        'status',
        'priority',
        'starts_at',
        'ends_at',
        'source_updated_at',
        'payload',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'source_updated_at' => 'datetime',
        'payload' => 'array',
    ];
}

