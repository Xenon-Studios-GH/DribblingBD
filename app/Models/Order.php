<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'customer_name',
        'phone',
        'address',
        'products',
        'dtf',
        'dtf_name',
        'dtf_number',
        'patch',
        'patch_price',
        'total_amount',
        'advanced_payment',
        'pending_payment',
        'payment_method',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'products' => 'array',
            'dtf' => 'boolean',
            'patch' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_no';
    }

    public static function generateOrderNo(): string
    {
        $last = static::lockForUpdate()->latest('id')->value('order_no');
        $next = $last ? ((int) substr($last, 15)) + 1 : 1;
        return 'DribblingOrder-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
