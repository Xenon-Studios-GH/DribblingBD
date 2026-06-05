<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            'date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FinanceTransactionVersion::class, 'transaction_id');
    }

    public function scopeIncome($q)
    {
        return $q->where('type', 'income');
    }

    public function scopeExpense($q)
    {
        return $q->where('type', 'expense');
    }

    public function scopeLastYear($q)
    {
        return $q->where('date', '>=', now()->subYear());
    }

    public function scopeLastDays($q, int $days)
    {
        return $q->where('date', '>=', now()->subDays($days));
    }
}
