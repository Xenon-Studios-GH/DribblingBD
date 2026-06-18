<?php

namespace App\Models;

use App\Traits\HasKeyValueSettings;
use Illuminate\Database\Eloquent\Model;

class TrackingSetting extends Model
{
    use HasKeyValueSettings;

    protected $table = 'tracking_settings';
    public $timestamps = false;
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'type'];
}
