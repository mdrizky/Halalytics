<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
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
            'type' => 'required|in:ingredient_alert,product_reminder,general',
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
        // 1. Send via Firebase
        $result = $this->firebaseService->sendToAll(
            $notification->title,
            $notification->body,
            ['type' => $notification->type]
        );

        $notification->update([
            'status' => $result['success'] ? 'sent' : 'failed',
            'sent_at' => now(),
            'sent_count' => $result['success_count'] ?? 0,
        ]);

        // 2. Create Inbox Notification (For App History)
        // If target is all, we create a broadcast notification (user_id = null)
        // detailed logic for specific users can be added later if needed
        if ($notification->target_type === 'all') {
            \App\Models\Notification::create([
                'user_id' => null, // Broadcast to all
                'title' => $notification->title,
                'message' => $notification->body,
                'type' => $notification->type,
                'is_read' => false,
                'created_at' => now(),
            ]);
        }
    }

    public function destroy(PushNotification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted');
    }
}
