<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteCategory extends Model
{
    use HasSeo;
    protected $fillable = ['name', 'slug', 'parent_id', 'description', 'is_active', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function projects()
    {
        return $this->hasMany(WebsiteProject::class, 'category_id');
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

    public function scopeTopLevel($q)
    {
        return $q->whereNull('parent_id');
    }
}
