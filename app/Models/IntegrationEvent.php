<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationEvent extends Model
{
    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'occurred_at',
        'status',
        'payload',
        'headers',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'payload' => 'array',
        'headers' => 'array',
        'processed_at' => 'datetime',
    ];
}

