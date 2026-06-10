<?php

namespace App\Enums;

enum StockSize: string
{
    case S = 'S';
    case M = 'M';
    case L = 'L';
    case XL = 'XL';
    case XXL = 'XXL';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
