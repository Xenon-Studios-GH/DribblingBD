<?php

namespace App\Services;

use App\Models\LoginLog;

class LoginLogService
{
    public function recordLogin(string $email, bool $success, ?int $userId = null): LoginLog
    {
        return LoginLog::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_at' => now(),
            'status' => $success ? 'success' : 'failed',
        ]);
    }

    public function updateLogout(int $userId): void
    {
        LoginLog::where('user_id', $userId)
            ->whereNull('logout_at')
            ->update(['logout_at' => now(), 'status' => 'logout']);
    }

    public function getLogs(array $filters = [])
    {
        $query = LoginLog::with('user')
            ->where('login_at', '>=', now()->subDays(90))
            ->latest('login_at');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
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
