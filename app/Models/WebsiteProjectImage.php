<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteProjectImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['project_id', 'image_path', 'sort_order'];

    public function project()
    {
        return $this->belongsTo(WebsiteProject::class, 'project_id');
    }
}
