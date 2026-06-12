<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingImageDeletion extends Model
{
    protected $fillable = ['file_path', 'disk', 'scheduled_for_deletion_at'];

    protected $casts = [
        'scheduled_for_deletion_at' => 'datetime',
    ];
}
