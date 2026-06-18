<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingSetting extends Model
{
    protected $table = 'tracking_settings';
    public $timestamps = false;
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'type'];

    public static function getValue(string $key, $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value, string $type = 'text'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
    }

    public function getBoolValue(string $key, bool $default = false): bool
    {
        $val = static::getValue($key);
        if ($val === null) return $default;
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }
}
