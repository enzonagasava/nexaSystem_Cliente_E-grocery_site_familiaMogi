<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EGroceryImage extends Model
{
    protected $table = 'e_grocery_images';

    protected $fillable = [
        'external_image_id',
        'storage_key',
        'url',
        'mime_type',
        'width',
        'height',
        'checksum',
        'source_updated_at',
        'payload',
    ];

    protected $casts = [
        'source_updated_at' => 'datetime',
        'payload' => 'array',
    ];
}

