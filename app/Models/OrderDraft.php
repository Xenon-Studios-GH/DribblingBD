<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDraft extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
