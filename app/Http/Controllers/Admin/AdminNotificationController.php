<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Get all notifications for admin
     */
    public function index(Request $request)
    {
        $notifications = AdminNotification::orderByDesc('created_at')
            ->limit(20)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    
    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $count = AdminNotification::where('is_read', false)->count();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        AdminNotification::where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
    
    /**
     * Create a new notification (helper for other controllers)
     */
    public static function createNotification($type, $title, $message, $data = [])
    {
        return AdminNotification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false
        ]);
    }
}
