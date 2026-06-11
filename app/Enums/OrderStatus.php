<?php

namespace App\Enums;

enum OrderStatus: string
{
    case OnHold = 'on_hold';
    case OutOfStock = 'out_of_stock';
    case Processing = 'processing';
    case Picked = 'picked';
    case Delivered = 'delivered';
    case Return = 'return';
    case Draft = 'draft';

    public function label(): string
    {
        return match ($this) {
            self::OnHold => 'On Hold',
            self::OutOfStock => 'Out of Stock',
            self::Processing => 'Processing',
            self::Picked => 'Picked',
            self::Delivered => 'Delivered',
            self::Return => 'Return',
            self::Draft => 'Draft',
        };
    }
}
