<?php

namespace App\Services\Finance;

use App\Enums\UserRole;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function notifyAdmins(string $type, string $title, string $message, ?string $relatedType = null, ?int $relatedId = null): void
    {
        $adminUserIds = User::whereIn('role', [UserRole::Superadmin->value, UserRole::Admin->value])
            ->where('status', true)
            ->pluck('id');

        if ($adminUserIds->isEmpty()) {
            return;
        }

        $now = now();
        $records = $adminUserIds->map(fn($userId) => [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'is_read' => false,
            'created_at' => $now,
        ])->toArray();

        Notification::insert($records);
    }
}
