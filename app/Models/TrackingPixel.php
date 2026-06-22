<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackingPixel extends Model
{
    protected $table = 'tracking_pixels';

    protected $fillable = [
        'platform', 'name', 'pixel_id', 'is_active',
        'load_position', 'options', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'options' => 'array',
            'sort_order' => 'integer',
            'pixel_id' => 'encrypted',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function eventLogs(): HasMany
    {
        return $this->hasMany(TrackingEventLog::class, 'pixel_id');
    }

    public function getPixelIdMaskedAttribute(): string
    {
        $id = $this->pixel_id;
        $len = strlen($id);
        if ($len <= 4) return str_repeat('*', $len);
        return substr($id, 0, 2) . str_repeat('*', $len - 4) . substr($id, -2);
    }

    public function getPlatformLabelAttribute(): string
    {
        return [
            'meta' => 'Meta Pixel',
            'ga4' => 'Google Analytics 4',
            'gtm' => 'Google Tag Manager',
            'google_ads' => 'Google Ads',
            'clarity' => 'Microsoft Clarity',
        ][$this->platform] ?? ucfirst($this->platform);
    }
}
