<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("site_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
        Cache::forget('site_settings');
    }

    public static function getShippingRates(): array
    {
        return [
            'dhaka_rate' => (float) (self::getValue('shipping_dhaka_rate', '100')),
            'outside_rate' => (float) (self::getValue('shipping_outside_rate', '120')),
            'free_threshold' => (float) (self::getValue('shipping_free_threshold', '3000')),
        ];
    }

    public static function calculateDeliveryCharge(float $totalAmount, ?string $city): float
    {
        $rates = self::getShippingRates();
        if ($totalAmount >= $rates['free_threshold']) {
            return 0;
        }
        if ($city && strtolower(trim($city)) === 'dhaka') {
            return $rates['dhaka_rate'];
        }
        return $rates['outside_rate'];
    }
}
