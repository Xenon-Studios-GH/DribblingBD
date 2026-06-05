<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'reference_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
