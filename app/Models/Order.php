<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_no',
        'customer_name',
        'phone',
        'address',
        'city',
        'products',
        'dtf',
        'dtf_name',
        'dtf_number',
        'patch',
        'patch_price',
        'total_amount',
        'advanced_payment',
        'pending_payment',
        'delivery_charge',
        'payment_method',
        'status',
        'notes',
        'created_by',
        'auto_restored_at',
    ];

    protected function casts(): array
    {
        return [
            'products' => 'array',
            'dtf' => 'boolean',
            'patch' => 'boolean',
            'total_amount' => 'decimal:2',
            'advanced_payment' => 'decimal:2',
            'pending_payment' => 'decimal:2',
            'patch_price' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'auto_restored_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_no';
    }

    public static function generateOrderNo(): string
    {
        return DB::transaction(function () {
            // Must include soft-deleted rows — the order_no UNIQUE constraint
            // applies to ALL rows including soft-deleted ones.
            $maxId = static::withTrashed()->lockForUpdate()->max('id');
            $nextNumber = ($maxId ?? 0) + 1;
            return 'DribblingOrder-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
