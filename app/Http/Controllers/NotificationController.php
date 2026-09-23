<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ==========================================
    // INDEX — Notifications page
    // ==========================================

    public function index(Request $request)
    {
        $query = Notification::forUser(auth()->id())
            ->latest();

        // Filter: all | unread
        $filter = $request->query('filter', 'all');

        if ($filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->limit(50)->get();

        // Unread count for badge
        $unreadCount = Notification::forUser(auth()->id())->unread()->count();

        // Highest ID (for polling)
        $lastMessageId = $notifications->max('id') ?? 0;

        // Convert to API array for Alpine
        $notificationsJson = $notifications->map(fn($n) => $n->toApiArray())->values();

        return view('notifications.index', compact(
            'notifications',
            'notificationsJson',
            'unreadCount',
            'lastMessageId',
            'filter'
        ));
    }

    // ==========================================
    // POLL — For auto-refresh (JSON)
    // ==========================================

    public function poll(Request $request)
    {
        $lastId = (int) $request->query('last_id', 0);
        $userId = auth()->id();

        // Latest notifications newer than lastId
        $newNotifs = Notification::forUser($userId)
            ->where('id', '>', $lastId)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        // Always return fresh unread count
        $unreadCount = Notification::forUser($userId)->unread()->count();

        return response()->json([
            'success'       => true,
            'unread_count'  => $unreadCount,
            'notifications' => $newNotifs->map(fn($n) => $n->toApiArray())->values(),
            'latest_id'     => $newNotifs->max('id') ?? $lastId,
        ]);
    }

    // ==========================================
    // MARK READ — Single notification
    // ==========================================

    public function markRead(Notification $notification)
    {
        // Security: ensure owns notification
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsRead();

        return response()->json([
            'success'      => true,
            'unread_count' => Notification::forUser(auth()->id())->unread()->count(),
        ]);
    }

    // ==========================================
    // MARK UNREAD — Single notification (optional)
    // ==========================================

    public function markUnread(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsUnread();

        return response()->json([
            'success'      => true,
            'unread_count' => Notification::forUser(auth()->id())->unread()->count(),
        ]);
    }

    // ==========================================
    // MARK ALL READ
    // ==========================================

    public function markAllRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success'      => true,
            'unread_count' => 0,
        ]);
    }

    // ==========================================
    // DELETE — Single notification
    // ==========================================

    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->delete();

        return response()->json([
            'success'      => true,
            'unread_count' => Notification::forUser(auth()->id())->unread()->count(),
        ]);
    }

    // ==========================================
    // DELETE ALL — Clear all notifications
    // ==========================================

    public function clearAll()
    {
        Notification::forUser(auth()->id())->delete();

        return response()->json([
            'success'      => true,
            'unread_count' => 0,
        ]);
    }
}