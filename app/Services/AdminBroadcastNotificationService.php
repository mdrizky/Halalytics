<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminBroadcastNotificationService
{
    public function __construct(
        private readonly FirebaseService $firebaseService,
        private readonly FirebaseRealtimeService $firebaseRealtimeService
    ) {}

    /**
     * Create broadcast inbox notification + realtime + FCM.
     */
    public function broadcast(string $title, string $message, string $type = 'general', array $extraData = []): array
    {
        try {
            $notification = Notification::create([
                'user_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'extra_data' => $extraData,
                'is_read' => false,
                'action_type' => $extraData['action_type'] ?? null,
                'action_value' => $extraData['action_value'] ?? null,
            ]);

            $this->firebaseRealtimeService->syncNotification($notification);

            $payload = array_merge([
                'type' => $type,
                'title' => $title,
                'body' => $message,
            ], $extraData);

            $result = $this->firebaseService->sendToAll($title, $message, $payload);

            $notification->update([
                'is_sent_fcm' => (bool)($result['success'] ?? false),
                'sent_at' => now(),
            ]);
            return $result;
        } catch (\Throwable $e) {
            Log::error('Admin broadcast notification failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
