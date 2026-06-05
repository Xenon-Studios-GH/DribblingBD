<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public const SIZES = ['S', 'M', 'L', 'XL', 'XXL'];

    protected $fillable = [
        'product_id',
        'size',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    protected $touches = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
