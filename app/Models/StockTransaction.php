<?php

namespace App\Models;

use App\Enums\StockSize;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'type', 'size',
        'quantity', 'stock_before', 'stock_after', 'note',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'size' => StockSize::class,
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query, int $days = 90)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
