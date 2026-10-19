<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    /**
     * Send notification to Admins (user_id = null).
     */
    public static function sendToAdmin(
        string $title,
        string $message,
        ?string $url = null,
        string $type = 'admin_alert',
        string $icon = 'fa-bell',
        ?array $data = null
    ): AppNotification {
        return AppNotification::create([
            'user_id' => null,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'url' => $url,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to a specific Customer user.
     */
    public static function sendToUser(
        int|User $user,
        string $title,
        string $message,
        ?string $url = null,
        string $type = 'customer_alert',
        string $icon = 'fa-bell',
        ?array $data = null
    ): AppNotification {
        $userId = $user instanceof User ? $user->id : $user;

        return AppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'url' => $url,
            'data' => $data,
            'is_read' => false,
        ]);
    }
}
