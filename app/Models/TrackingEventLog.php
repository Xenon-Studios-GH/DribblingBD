<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEventLog extends Model
{
    protected $table = 'tracking_events_log';

    protected $fillable = [
        'pixel_id', 'event_name', 'event_data',
        'response', 'status', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'event_data' => 'array',
            'response' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function pixel(): BelongsTo
    {
        return $this->belongsTo(TrackingPixel::class, 'pixel_id');
    }
}
