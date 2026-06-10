<?php

namespace App\Enums;

enum OrderStatus: string
{
    case OnHold = 'on_hold';
    case OutOfStock = 'out_of_stock';
    case Packed = 'packed';
    case Picked = 'picked';
    case Delivered = 'delivered';
    case Draft = 'draft';

    public function label(): string
    {
        return match ($this) {
            self::OnHold => 'On Hold',
            self::OutOfStock => 'Out of Stock',
            self::Packed => 'Packed',
            self::Picked => 'Picked',
            self::Delivered => 'Delivered',
            self::Draft => 'Draft',
        };
    }
}
