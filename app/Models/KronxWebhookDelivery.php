<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KronxWebhookDelivery extends Model
{
    protected $fillable = [
        'delivery_uuid',
        'event',
        'payload',
        'status',
        'error',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
