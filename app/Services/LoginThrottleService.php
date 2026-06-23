<?php

namespace App\Services;

use App\Models\LoginTrap;
use Illuminate\Support\Facades\Cache;

class LoginThrottleService
{
    private const TIERS = [
        10 => 0,        // 1-10: no lockout
        20 => 60,       // 11-20: 1 minute
        25 => 300,      // 21-25: 5 minutes
        28 => 900,      // 26-28: 15 minutes
        PHP_INT_MAX => 28800, // 29+: 8 hours
    ];

    private const TRAP_THRESHOLD = 100;

    private function cycleKey(string $email, string $ip): string
    {
        return 'login_cycle:' . md5($email) . ':' . $ip;
    }

    private function totalKey(string $email, string $ip): string
    {
        return 'login_total:' . md5($email) . ':' . $ip;
    }

    private function lockKey(string $email, string $ip): string
    {
        return 'login_locked:' . md5($email) . ':' . $ip;
    }

    private function getCycle(string $email, string $ip): array
    {
        return Cache::get($this->cycleKey($email, $ip), ['attempts' => 0, 'started_at' => now()->timestamp]);
    }

    private function setCycle(string $email, string $ip, array $data): void
    {
        Cache::put($this->cycleKey($email, $ip), $data, 86400);
    }

    public function getTotalAttempts(string $email, string $ip): int
    {
        return (int) Cache::get($this->totalKey($email, $ip), 0);
    }

    /**
     * Check if login is currently allowed for this email+IP.
     * Returns ['allowed' => bool, 'remaining' => int seconds, 'tier' => int|null, 'total' => int]
     */
    public function check(string $email, string $ip): array
    {
        $this->checkDailyReset($email, $ip);

        $cycle = $this->getCycle($email, $ip);
        $lockedUntil = Cache::get($this->lockKey($email, $ip));
        $total = $this->getTotalAttempts($email, $ip);

        // Check if currently locked
        if ($lockedUntil && now()->timestamp < $lockedUntil) {
            return [
                'allowed' => false,
                'remaining' => $lockedUntil - now()->timestamp,
                'tier' => $this->determineTier($cycle['attempts']),
                'total' => $total,
            ];
        }

        // Lockout expired — reset cycle
        if ($lockedUntil && now()->timestamp >= $lockedUntil) {
            $cycle = ['attempts' => 0, 'started_at' => now()->timestamp];
            $this->setCycle($email, $ip, $cycle);
            Cache::forget($this->lockKey($email, $ip));
        }

        return [
            'allowed' => true,
            'remaining' => 0,
            'tier' => null,
            'total' => $total,
        ];
    }

    /**
     * Record a failed attempt. If lockout triggered, returns lockout info.
     * If trap threshold reached, triggers trap.
     */
    public function increment(string $email, string $ip): array
    {
        $this->checkDailyReset($email, $ip);

        $cycle = $this->getCycle($email, $ip);
        $cycle['attempts']++;
        $this->setCycle($email, $ip, $cycle);

        // Increment total
        $total = $this->getTotalAttempts($email, $ip) + 1;
        Cache::put($this->totalKey($email, $ip), $total, 86400);

        // Determine lockout
        $lockoutDuration = $this->getLockoutDuration($cycle['attempts']);
        $tier = $this->determineTier($cycle['attempts']);

        if ($lockoutDuration > 0) {
            $lockedUntil = now()->timestamp + $lockoutDuration;
            Cache::put($this->lockKey($email, $ip), $lockedUntil, $lockoutDuration + 60);
            return [
                'locked' => true,
                'remaining' => $lockoutDuration,
                'tier' => $tier,
                'total' => $total,
                'trap_triggered' => false,
            ];
        }

        // Check trap threshold
        $trapTriggered = false;
        if ($total >= self::TRAP_THRESHOLD) {
            $this->triggerTrap($email, $ip, 'excessive_attempts');
            $trapTriggered = true;
        }

        return [
            'locked' => false,
            'remaining' => 0,
            'tier' => null,
            'total' => $total,
            'trap_triggered' => $trapTriggered,
        ];
    }

    /**
     * Reset counters on successful login.
     */
    public function reset(string $email, string $ip): void
    {
        Cache::forget($this->cycleKey($email, $ip));
        Cache::forget($this->totalKey($email, $ip));
        Cache::forget($this->lockKey($email, $ip));
    }

    /**
     * Check if this email+IP should be trapped (recently locked out but now logging in).
     */
    public function shouldTrapOnLogin(string $email, string $ip): bool
    {
        if (LoginTrap::forIp($ip)->where('attempted_email', $email)->active()->exists()) {
            return true;
        }
        $lockedUntil = Cache::get($this->lockKey($email, $ip));
        if ($lockedUntil && $lockedUntil > now()->subDay()->timestamp) {
            return true;
        }
        return false;
    }

    public function triggerTrap(string $email, string $ip, string $reason): void
    {
        LoginTrap::create([
            'ip_address' => $ip,
            'attempted_email' => $email,
            'trigger_reason' => $reason,
            'status' => 'active',
        ]);
    }

    private function checkDailyReset(string $email, string $ip): void
    {
        $cycle = $this->getCycle($email, $ip);
        $sixAmToday = now()->copy()->setHour(6)->setMinute(0)->setSecond(0)->timestamp;

        if ($cycle['started_at'] < $sixAmToday && now()->timestamp >= $sixAmToday) {
            Cache::forget($this->cycleKey($email, $ip));
            Cache::forget($this->totalKey($email, $ip));
            Cache::forget($this->lockKey($email, $ip));
        }
    }

    private function getLockoutDuration(int $attempts): int
    {
        foreach (self::TIERS as $threshold => $duration) {
            if ($attempts <= $threshold) {
                return $duration;
            }
        }
        return end(self::TIERS);
    }

    private function determineTier(int $attempts): ?int
    {
        if ($attempts <= 10) return null;
        if ($attempts <= 20) return 1;
        if ($attempts <= 25) return 2;
        if ($attempts <= 28) return 3;
        return 4;
    }
}
