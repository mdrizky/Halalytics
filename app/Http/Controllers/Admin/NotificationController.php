<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Services\AdminBroadcastNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(AdminBroadcastNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = PushNotification::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:ingredient_alert,product_reminder,general,product,poster,news',
            'target_type' => 'required|in:all,specific_users',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $notification = PushNotification::create($validated);

        // Send immediately if not scheduled
        if (!$request->scheduled_at) {
            $this->sendNotification($notification);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifikasi berhasil dibuat!');
    }

    protected function sendNotification($notification)
    {
        // Send via service: inbox + realtime + fcm
        $result = $this->notificationService->broadcast(
            $notification->title,
            $notification->body,
            $notification->type,
            [
                'source' => 'admin_panel',
                'target_type' => $notification->target_type,
            ]
        );

        $notification->update([
            'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
            'sent_at' => now(),
            'sent_count' => $result['success_count'] ?? 0,
        ]);
    }

    public function destroy(PushNotification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted');
    }
}
