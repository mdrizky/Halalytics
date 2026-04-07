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
            'user_ids' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $userIds = collect(explode(',', (string) ($validated['user_ids'] ?? '')))
            ->map(fn ($id) => trim($id))
            ->filter(fn ($id) => $id !== '')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        if (($validated['target_type'] ?? 'all') === 'specific_users' && empty($userIds)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_ids' => 'Isi minimal satu User ID untuk target pengguna tertentu.']);
        }

        $notification = PushNotification::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'type' => $validated['type'],
            'target_type' => $validated['target_type'],
            'target_data' => ['user_ids' => $userIds],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        // Send immediately if not scheduled
        if (!$request->scheduled_at) {
            $this->sendNotification($notification);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifikasi berhasil dibuat!');
    }

    public function show($id)
    {
        $notification = PushNotification::findOrFail($id);
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit($id)
    {
        $notification = PushNotification::findOrFail($id);
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:ingredient_alert,product_reminder,general,product,poster,news',
            'target_type' => 'required|in:all,specific_users',
            'user_ids' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $notification = PushNotification::findOrFail($id);

        $userIds = collect(explode(',', (string) ($validated['user_ids'] ?? '')))
            ->map(fn ($id) => trim($id))
            ->filter(fn ($id) => $id !== '')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        $notification->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'type' => $validated['type'],
            'target_type' => $validated['target_type'],
            'target_data' => ['user_ids' => $userIds],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifikasi berhasil diperbarui!');
    }

    protected function sendNotification($notification)
    {
        $extraData = [
            'source' => 'admin_panel',
            'target_type' => $notification->target_type,
        ];

        if ($notification->target_type === 'specific_users') {
            $userIds = collect((array) ($notification->target_data['user_ids'] ?? []))
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->values()
                ->all();

            $result = $this->notificationService->broadcastToUsers(
                $userIds,
                $notification->title,
                $notification->body,
                $notification->type,
                $extraData
            );
        } else {
            $result = $this->notificationService->broadcast(
                $notification->title,
                $notification->body,
                $notification->type,
                $extraData
            );
        }

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
