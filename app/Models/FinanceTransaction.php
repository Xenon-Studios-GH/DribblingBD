<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'category_id', 'amount',
        'description', 'date', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => \App\Enums\FinanceType::class,
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function scopeIncome($q)
    {
        return $q->where('type', 'income');
    }

    public function scopeExpense($q)
    {
        return $q->where('type', 'expense');
    }
}
