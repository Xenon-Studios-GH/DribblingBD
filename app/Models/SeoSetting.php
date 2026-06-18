<?php

namespace App\Models;

use App\Traits\HasKeyValueSettings;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasKeyValueSettings;

    protected $table = 'seo_settings';
    public $timestamps = false;
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'type'];
}
