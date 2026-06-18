<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceChartPreference extends Model
{
    protected $fillable = ['user_id', 'type', 'selected_category_ids'];

    protected $casts = [
        'selected_category_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
