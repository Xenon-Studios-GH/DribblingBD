<?php

namespace App\Traits;

trait HasKeyValueSettings
{
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
