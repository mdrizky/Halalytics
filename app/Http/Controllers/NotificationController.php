<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 📋 Get user notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = min($request->get('limit', 20), 50);
        
        $notifications = $this->notificationService->getUserNotifications($user, $limit);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'has_more' => count($notifications) >= $limit,
            ]
        ]);
    }

    /**
     * 🔢 Get unread notification count
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count,
            ]
        ]);
    }

    /**
     * ✅ Mark notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification marked as read' : 'Notification not found',
        ]);
    }

    /**
     * ✅ Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'data' => [
                'marked_count' => $count,
            ]
        ]);
    }

    /**
     * 🗑️ Delete notification
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->deleteNotification($id, $user);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification deleted' : 'Notification not found',
        ]);
    }

    /**
     * 🧹 Clean old notifications (admin only)
     */
    public function cleanOld(Request $request): JsonResponse
    {
        $this->authorize('admin');
        
        $daysOld = min($request->get('days_old', 30), 90);
        $deleted = $this->notificationService->cleanOldNotifications($daysOld);

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old notifications",
            'data' => [
                'deleted_count' => $deleted,
                'days_old' => $daysOld,
            ]
        ]);
    }

    /**
     * 📊 Get notification statistics (admin only)
     */
    public function stats(): JsonResponse
    {
        $this->authorize('admin');
        
        $stats = $this->notificationService->getNotificationStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * 🔔 Send test notification (admin only)
     */
    public function sendTest(Request $request): JsonResponse
    {
        $this->authorize('admin');
        
        $request->validate([
            'user_id' => 'required|exists:users,id_user',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'type' => 'string|max:50',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $type = $request->get('type', 'test');
        
        $notification = $this->notificationService->sendNotification(
            $user,
            $type,
            $request->title,
            $request->message,
            ['test' => true, 'sent_by' => Auth::id()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent',
            'data' => [
                'notification_id' => $notification->id,
                'user_id' => $user->id_user,
            ]
        ]);
    }
}
