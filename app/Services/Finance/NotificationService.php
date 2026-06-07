<?php

namespace App\Services\Finance;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function notifyAdmins(string $type, string $title, string $message, ?string $relatedType = null, ?int $relatedId = null): void
    {
        $adminUserIds = User::whereIn('role', ['superadmin', 'admin'])
            ->where('status', true)
            ->pluck('id');

        $notifications = $adminUserIds->map(fn($userId) => [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'is_read' => false,
            'created_at' => now(),
        ])->toArray();

        Notification::insert($notifications);
    }
}
