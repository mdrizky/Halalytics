<?php

namespace App\Services;

use App\Models\ActivityEvent;
use Illuminate\Support\Facades\Log;

class ActivityEventService
{
    public function __construct(private FirebaseRealtimeService $firebaseRealtimeService)
    {
    }

    public function logEvent(
        string $eventType,
        ?int $userId,
        ?string $username,
        ?string $entityRef,
        ?string $summary,
        string $status = 'success',
        array $payload = []
    ): ActivityEvent {
        $event = ActivityEvent::create([
            'event_type' => $eventType,
            'user_id' => $userId,
            'username' => $username,
            'entity_ref' => $entityRef,
            'summary' => $summary,
            'status' => $status,
            'payload_json' => $payload,
            'created_at' => now(),
        ]);

        try {
            $this->firebaseRealtimeService->pushAdminActivityEvent($event);
            $this->firebaseRealtimeService->incrementAdminEventStats($eventType, $status, $payload);
        } catch (\Throwable $e) {
            Log::warning('Failed realtime push for activity_event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $event;
    }
}
