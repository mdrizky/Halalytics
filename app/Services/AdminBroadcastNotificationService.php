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

    /**
     * Send notification to specific users only (inbox + realtime + FCM).
     */
    public function broadcastToUsers(array $userIds, string $title, string $message, string $type = 'general', array $extraData = []): array
    {
        $targetIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($targetIds->isEmpty()) {
            return ['success' => false, 'message' => 'No valid target users'];
        }

        $successCount = 0;
        foreach ($targetIds as $userId) {
            try {
                $user = User::where('id_user', $userId)->orWhere('id', $userId)->first();
                if (!$user) {
                    continue;
                }

                $resolvedUserId = (int) ($user->id_user ?? $user->id);
                $notification = Notification::create([
                    'user_id' => $resolvedUserId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'extra_data' => $extraData,
                    'is_read' => false,
                    'action_type' => $extraData['action_type'] ?? null,
                    'action_value' => $extraData['action_value'] ?? null,
                ]);

                $this->firebaseRealtimeService->syncNotification($notification);
                $this->firebaseService->sendToUser($resolvedUserId, $title, $message, array_merge([
                    'type' => $type,
                    'title' => $title,
                    'body' => $message,
                ], $extraData));

                $notification->update([
                    'is_sent_fcm' => true,
                    'sent_at' => now(),
                ]);

                $successCount++;
            } catch (\Throwable $e) {
                Log::error('Admin specific notification failed for user ' . $userId . ': ' . $e->getMessage());
            }
        }

        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'failure_count' => max(0, $targetIds->count() - $successCount),
        ];
    }
}
