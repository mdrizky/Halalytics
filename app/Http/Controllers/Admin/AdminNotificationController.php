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

        $mapped = $notifications->map(function (AdminNotification $notification) {
            $payload = is_array($notification->data) ? $notification->data : [];

            $targetUrl = match($notification->type) {
                'report' => isset($payload['report_id']) ? route('admin.report.index') . '#report-' . $payload['report_id'] : route('admin.report.index'),
                'user' => route('admin.users.dashboard'),
                'product' => route('admin.product.index'),
                'scan' => route('admin.scan.index'),
                'bpom', 'verification' => route('admin.bpom.index'),
                'street_food' => route('admin.street-foods.index'),
                'request' => route('admin.requests.index'),
                'article', 'news' => route('admin.promo.blog.index'),
                default => route('admin.dashboard')
            };

            $detail = null;
            $detail = $payload['detail'] ?? $payload['description'] ?? null;

            if (!empty($payload['action_type']) && !empty($payload['action_value'])) {
                if (in_array($payload['action_type'], ['open_news', 'open_article'], true)) {
                    $targetUrl = route('admin.promo.blog.index');
                } elseif (in_array($payload['action_type'], ['open_bpom', 'open_verification'], true)) {
                    $targetUrl = route('admin.bpom.index');
                } elseif (in_array($payload['action_type'], ['view_request', 'open_request'], true)) {
                    $targetUrl = route('admin.requests.index');
                }
            }

            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'detail' => $detail,
                'data' => $payload,
                'target_url' => $targetUrl,
                'is_read' => (bool) $notification->is_read,
                'read_at' => optional($notification->read_at)?->toIso8601String(),
                'created_at' => optional($notification->created_at)?->toIso8601String(),
                'relative_time' => optional($notification->created_at)?->diffForHumans(),
                'icon' => match($notification->type) {
                    'report' => 'flag',
                    'user' => 'person',
                    'product' => 'inventory_2',
                    'scan' => 'qr_code_scanner',
                    'bpom' => 'verified_user',
                    'street_food' => 'restaurant',
                    default => 'notifications'
                },
                'color' => match($notification->type) {
                    'report' => 'amber',
                    'user' => 'sky',
                    'product' => 'emerald',
                    'scan' => 'indigo',
                    'bpom' => 'teal',
                    'street_food' => 'orange',
                    default => 'slate'
                },
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $mapped
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
