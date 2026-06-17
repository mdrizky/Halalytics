<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivityLogger
{
    public static function log(
        string $eventType,
        ?int $userId,
        ?string $username = null,
        ?string $summary = null,
        ?string $status = null,
        ?array $payload = null,
        ?string $entityRef = null
    ): void {
        try {
            DB::table('activity_events')->insert([
                'event_type' => $eventType,
                'user_id' => $userId,
                'username' => $username,
                'entity_ref' => $entityRef,
                'summary' => $summary,
                'status' => $status,
                'payload_json' => $payload ? json_encode($payload) : null,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('ActivityLogger failed: ' . $e->getMessage());
        }
    }

    public static function scan(string $productName, string $barcode, string $status, User $user): void
    {
        self::log(
            eventType: 'legacy_scan',
            userId: $user->id_user,
            username: $user->username ?? $user->full_name,
            summary: "Scan produk: {$productName}",
            status: $status,
            payload: ['barcode' => $barcode, 'product_name' => $productName],
            entityRef: $barcode
        );
    }

    public static function drugInteraction(string $drugA, string $drugB, ?string $severity, User $user): void
    {
        self::log(
            eventType: 'drug_interaction',
            userId: $user->id_user,
            username: $user->username ?? $user->full_name,
            summary: "Cek interaksi: {$drugA} + {$drugB}",
            status: $severity,
            payload: ['drug_a' => $drugA, 'drug_b' => $drugB, 'severity' => $severity]
        );
    }

    public static function skincareAnalysis(?string $productName, User $user): void
    {
        self::log(
            eventType: 'skincare_analysis',
            userId: $user->id_user,
            username: $user->username ?? $user->full_name,
            summary: $productName ? "Analisis skincare: {$productName}" : 'Analisis skincare',
            payload: $productName ? ['product_name' => $productName] : null
        );
    }

    public static function healthCheck(string $type, User $user): void
    {
        self::log(
            eventType: 'health_risk_score',
            userId: $user->id_user,
            username: $user->username ?? $user->full_name,
            summary: "Pengecekan kesehatan: {$type}"
        );
    }
}
