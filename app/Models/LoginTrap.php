<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginTrap extends Model
{
    protected $fillable = [
        'ip_address',
        'attempted_email',
        'trigger_reason',
        'trapped_at',
        'released_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'trapped_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function release(): void
    {
        $this->update([
            'status' => 'released',
            'released_at' => now(),
        ]);
    }
}
