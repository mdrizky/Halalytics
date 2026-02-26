<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityEvent;

class FirebaseRealtimeService
{
    protected $database;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials.file');
        
        if ($credentialsPath && file_exists($credentialsPath) && is_readable($credentialsPath)) {
            try {
                $factory = (new Factory)
                    ->withServiceAccount($credentialsPath)
                    ->withDatabaseUri(config('firebase.database.url'));

                $this->database = $factory->createDatabase();
            } catch (\Exception $e) {
                Log::error("Firebase Initialization Failed: " . $e->getMessage());
                $this->database = null;
            }
        } else {
            Log::warning("Firebase credentials file not found or not readable: $credentialsPath. Realtime sync disabled.");
            $this->database = null;
        }
    }

    /**
     * Sync notification to Firebase
     */
    public function syncNotification($notification)
    {
        if (!$this->database) return null;

        $ref = $this->database
            ->getReference('notifications/' . ($notification->user_id ?? 'broadcast'))
            ->push([
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at->timestamp,
                'action_type' => $notification->action_type,
                'action_value' => $notification->action_value,
            ]);

        // Save Firebase key back to DB
        $notification->update(['firebase_key' => $ref->getKey()]);

        return $ref->getKey();
    }

    /**
     * Sync scan history to Firebase
     */
    public function syncScanHistory($scanHistory)
    {
        if (!$this->database) return null;

        $ref = $this->database
            ->getReference('scan_histories/' . $scanHistory->user_id)
            ->push([
                'id' => $scanHistory->id,
                'product_name' => $scanHistory->product_name,
                'product_image' => $scanHistory->product_image,
                'halal_status' => $scanHistory->halal_status,
                'source' => $scanHistory->source,
                'scan_method' => $scanHistory->scan_method,
                'created_at' => $scanHistory->created_at->timestamp,
            ]);

        $scanHistory->update([
            'firebase_key' => $ref->getKey(),
            'is_synced' => true
        ]);

        return $ref->getKey();
    }

    /**
     * Sync favorite to Firebase
     */
    public function syncFavorite($favorite)
    {
        if (!$this->database) return null;

        $ref = $this->database
            ->getReference('favorites/' . $favorite->user_id)
            ->push([
                'id' => $favorite->id,
                'product_name' => $favorite->product_name,
                'product_image' => $favorite->product_image,
                'halal_status' => $favorite->halal_status,
                'has_status_changed' => $favorite->has_status_changed,
                'created_at' => $favorite->created_at->timestamp,
            ]);

        $favorite->update(['firebase_key' => $ref->getKey()]);

        return $ref->getKey();
    }

    /**
     * Broadcast UMKM submission update
     */
    public function broadcastUmkmUpdate($umkmProduct)
    {
        if (!$this->database) return;

        $this->database
            ->getReference('umkm_updates')
            ->push([
                'umkm_id' => $umkmProduct->id,
                'status' => $umkmProduct->verification_status,
                'verified_at' => $umkmProduct->verified_at?->timestamp,
                'timestamp' => now()->timestamp,
            ]);
    }

    /**
     * Update admin dashboard stats
     */
    public function updateAdminStats($stats)
    {
        if (!$this->database) return;

        $this->database
            ->getReference('admin/stats')
            ->set($stats);
    }

    /**
     * Push latest admin activity event for realtime dashboard feed.
     */
    public function pushAdminActivityEvent(ActivityEvent $event): void
    {
        if (!$this->database) return;

        $payload = [
            'id' => $event->id,
            'event_type' => $event->event_type,
            'user_id' => $event->user_id,
            'username' => $event->username,
            'entity_ref' => $event->entity_ref,
            'summary' => $event->summary,
            'status' => $event->status,
            'payload_json' => $event->payload_json,
            'created_at' => optional($event->created_at)->toIso8601String() ?? now()->toIso8601String(),
            'created_at_unix' => optional($event->created_at)->timestamp ?? now()->timestamp,
        ];

        $this->database->getReference('admin/activity_feed/latest')->set($payload);
        $this->database->getReference('admin/activity_feed/items')->push($payload);
    }

    /**
     * Increment aggregate counters for dashboard cards.
     */
    public function incrementAdminEventStats(string $eventType, string $status = 'success', array $payload = []): void
    {
        if (!$this->database) return;

        $ref = $this->database->getReference('admin/dashboard/stats');
        $current = $ref->getValue() ?? [];

        $map = [
            'external_scan' => 'total_external_scans',
            'skincare_analysis' => 'total_skincare_analyses',
            'drug_interaction' => 'total_interaction_checks',
        ];

        $field = $map[$eventType] ?? null;
        if ($field) {
            $current[$field] = (int)($current[$field] ?? 0) + 1;
        }

        if ($eventType === 'drug_interaction') {
            $severity = strtolower((string)($payload['severity'] ?? ''));
            if (in_array($severity, ['major', 'contraindicated'], true) && $status === 'success') {
                $current['major_or_contra_count'] = (int)($current['major_or_contra_count'] ?? 0) + 1;
            }
            $current['last_severity'] = $severity;
        }

        $current['last_event_type'] = $eventType;
        $current['last_updated_at'] = now()->toIso8601String();
        $ref->set($current);
    }
}
