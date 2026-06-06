<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function generateProductCode(): string
    {
        $last = static::lockForUpdate()->latest('id')->value('product_code');
        $next = $last ? ((int) substr($last, 10)) + 1 : 1;
        return 'Dribbling-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function project()
    {
        return $this->hasOne(WebsiteProject::class);
    }

    public function getSlugAttribute(): string
    {
        return $this->project?->slug ?? Str::slug($this->product_name);
    }
}
