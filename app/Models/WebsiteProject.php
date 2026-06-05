<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteProject extends Model
{
    protected $fillable = [
        'product_id', 'category_id', 'regular_price', 'offer_price',
        'details', 'slug', 'is_active', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'offer_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (empty($project->slug) && $project->product) {
                $project->slug = Str::slug($project->product->product_name) . '-' . $project->product_id;
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(WebsiteCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(WebsiteProjectImage::class, 'project_id')->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeHasImages($q)
    {
        return $q->whereHas('images');
    }
}
