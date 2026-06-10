<?php

namespace App\Enums;

enum UserRole: string
{
    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case Staff = 'staff';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Superadmin',
            self::Admin => 'Admin',
            self::Staff => 'Staff',
            self::Customer => 'Customer',
        };
    }

    public static function adminRoles(): array
    {
        return [self::Superadmin->value, self::Admin->value];
    }

    public static function allRoles(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function staffAndAbove(): array
    {
        return [self::Superadmin->value, self::Admin->value, self::Staff->value];
    }
}
