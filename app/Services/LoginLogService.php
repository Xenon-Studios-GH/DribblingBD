<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class LoginLogService
{
    public function recordLogin(string $email, bool $success, ?int $userId = null): ActivityLog
    {
        $ipAddress = null;
        $userAgent = null;

        if (app()->runningInConsole() || app()->runningUnitTests()) {
            $ipAddress = '127.0.0.1';
            $userAgent = 'CLI/Test';
        } else {
            $ipAddress = request()->ip();
            $userAgent = request()->userAgent();
        }

        return ActivityLog::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_at' => now(),
            'status' => $success ? 'success' : 'failed',
            'action' => 'login',
        ]);
    }

    public function updateLogout(?int $userId): void
    {
        if ($userId === null) {
            return;
        }

        DB::transaction(function () use ($userId) {
            $latest = ActivityLog::where('user_id', $userId)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->lockForUpdate()
                ->first();

            if ($latest) {
                $latest->update(['logout_at' => now(), 'status' => 'logout']);
            }
        });
    }

    public function getLogs(array $filters = [], int $days = 90)
    {
        $query = ActivityLog::with('user')
            ->where('login_at', '>=', now()->subDays($days))
            ->latest('login_at');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', $filters['email'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('login_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('login_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(20);
    }
}
