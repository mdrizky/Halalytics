<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function authUserId(Request $request): int
    {
        return (int)($request->user()->id_user ?? $request->user()->id ?? 0);
    }

    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $userId = $this->authUserId($request);
        $notifications = Notification::forUser($userId)
            ->with(['relatedProduct', 'relatedUmkm'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::forUser($userId)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id, Request $request)
    {
        $notification = Notification::where('user_id', $this->authUserId($request))
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(Request $request)
    {
        Notification::forUser($this->authUserId($request))
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Show single notification detail
     */
    public function show($id, Request $request)
    {
        $notification = Notification::where('user_id', $this->authUserId($request))
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    /**
     * Get unread count
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::forUser($this->authUserId($request))
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
