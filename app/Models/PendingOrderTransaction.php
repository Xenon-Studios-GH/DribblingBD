<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingOrderTransaction extends Model
{
    protected $fillable = [
        'order_id', 'order_no', 'customer_name',
        'total_amount', 'delivery_charge',
        'product_sales_amount', 'dtf_sales_amount', 'patch_sales_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'product_sales_amount' => 'decimal:2',
            'dtf_sales_amount' => 'decimal:2',
            'patch_sales_amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
