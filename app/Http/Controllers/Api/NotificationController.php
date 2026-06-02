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

    /**
     * Delete a notification
     */
    public function destroy($id, Request $request)
    {
        $notification = Notification::where('user_id', $this->authUserId($request))
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Mark notification as read (from POST body with notification_id or raw Int)
     */
    public function markReadFromBody(Request $request)
    {
        $id = $request->input('notification_id') ?? $request->input('id') ?? $request->getContent();
        $notification = Notification::where('user_id', $this->authUserId($request))
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Get recently added products (for background notification polling)
     */
    public function newProducts(Request $request)
    {
        $lastCheck = $request->input('last_check');
        $query = \App\Models\OCRProduct::orderBy('created_at', 'desc');

        if ($lastCheck) {
            $query->where('created_at', '>', date('Y-m-d H:i:s', $lastCheck / 1000));
        }

        $products = $query->take(10)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->product_name,
                'brand' => $p->brand,
                'barcode' => $p->barcode ?? '',
                'status' => $p->halal_status ?? 'unknown',
                'created_at' => $p->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get product status updates (for background notification polling)
     */
    public function statusUpdates(Request $request)
    {
        $lastCheck = $request->input('last_check');
        $query = Notification::where('user_id', $this->authUserId($request))
            ->where('type', 'status_update')
            ->orderBy('created_at', 'desc');

        if ($lastCheck) {
            $query->where('created_at', '>', date('Y-m-d H:i:s', $lastCheck / 1000));
        }

        $updates = $query->take(10)->get()->map(function ($n) {
            $payload = is_string($n->data) ? json_decode($n->data, true) : ($n->data ?? []);
            return [
                'productId' => $payload['product_id'] ?? '',
                'productName' => $payload['product_name'] ?? $n->title,
                'oldStatus' => $payload['old_status'] ?? '',
                'newStatus' => $payload['new_status'] ?? '',
                'updated_at' => $n->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $updates,
        ]);
    }
}
