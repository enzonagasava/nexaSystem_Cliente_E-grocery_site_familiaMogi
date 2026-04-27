<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EGroceryOrderExport extends Model
{
    protected $table = 'e_grocery_order_exports';

    protected $fillable = [
        'external_order_id',
        'source',
        'status',
        'request_payload',
        'normalized_payload',
        'panel_response',
        'panel_order_id',
        'attempt_count',
        'last_attempt_at',
        'exported_at',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'normalized_payload' => 'array',
        'panel_response' => 'array',
        'last_attempt_at' => 'datetime',
        'exported_at' => 'datetime',
    ];
}

