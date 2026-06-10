<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Bkash = 'bkash';
    case Nagad = 'nagad';
    case Rocket = 'rocket';
    case Cod = 'cod';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Bkash => 'bKash',
            self::Nagad => 'Nagad',
            self::Rocket => 'Rocket',
            self::Cod => 'Cash on Delivery',
            self::Cash => 'Cash',
        };
    }
}
