<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Poll real-time notifications for the current authenticated user.
     */
    public function poll(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'unread_count' => 0,
                'notifications' => [],
                'recent_alerts' => [],
            ]);
        }

        $user = Auth::user();
        $query = AppNotification::query();

        if ($user->isAdmin()) {
            $query->forAdmin();
        } else {
            $query->forUser($user->id);
        }

        $unreadCount = (clone $query)->unread()->count();

        $notifications = (clone $query)
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'title' => $item->title,
                    'message' => $item->message,
                    'icon' => $item->icon ?: 'fa-bell',
                    'url' => $item->url,
                    'is_read' => (bool) $item->is_read,
                    'time_ago' => $item->created_at ? $item->created_at->diffForHumans() : 'baru saja',
                    'created_at' => $item->created_at ? $item->created_at->toIso8601String() : null,
                ];
            });

        // Recent alerts created in the last 25 seconds for toast popups
        $threshold = Carbon::now()->subSeconds(25);
        $recentAlerts = (clone $query)
            ->unread()
            ->where('created_at', '>=', $threshold)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'title' => $item->title,
                    'message' => $item->message,
                    'icon' => $item->icon ?: 'fa-bell',
                    'url' => $item->url,
                    'created_at' => $item->created_at ? $item->created_at->toIso8601String() : null,
                ];
            });

        return response()->json([
            'authenticated' => true,
            'role' => $user->role,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
            'recent_alerts' => $recentAlerts,
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(Request $request, AppNotification $notification): JsonResponse
    {
        $user = Auth::user();

        // Security check
        if ($user->isAdmin() && $notification->user_id !== null) {
            abort(403);
        }
        if (!$user->isAdmin() && $notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi telah ditandai dibaca.',
        ]);
    }

    /**
     * Mark all notifications as read for current user/admin.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $query = AppNotification::query();

        if ($user->isAdmin()) {
            $query->forAdmin();
        } else {
            $query->forUser($user->id);
        }

        $query->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai dibaca.',
        ]);
    }
}
