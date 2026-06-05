<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'usercode',
        'name',
        'username',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'avatar',
        'address',
        'city',
        'state',
        'country',
        'shipping_address',
        'preferred_size',
        'favorite_team',
        'preferred_payment',
        'password',
        'wishlist',
        'cart',
        'orders',
        'newsletter',
        'status',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'wishlist' => 'array',
            'cart' => 'array',
            'orders' => 'array',
            'date_of_birth' => 'date',
            'status' => 'boolean',
            'newsletter' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateUsercode(): string
    {
        do {
            $code = 'dribbler-' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('usercode', $code)->lockForUpdate()->exists());

        return $code;
    }
}