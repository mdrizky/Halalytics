<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthTracking;
use App\Models\ProductModel;
use App\Models\ScanHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function syncScanLogs(Request $request)
    {
        $request->validate([
            'logs' => 'required|array|max:100',
            'logs.*.barcode' => 'required|string|max:255',
            'logs.*.product_name' => 'nullable|string|max:255',
            'logs.*.halal_status' => 'required|in:halal,haram,syubhat',
            'logs.*.ai_analysis' => 'nullable|string',
            'logs.*.scanned_at' => 'required|integer',
        ]);

        $userId = $request->user()->id_user;
        $createdCount = 0;

        DB::transaction(function () use ($request, $userId, &$createdCount) {
            foreach ($request->input('logs', []) as $log) {
                $product = ProductModel::firstOrCreate(
                    ['barcode' => $log['barcode']],
                    [
                        'nama_product' => $log['product_name'] ?? 'Unknown Product',
                        'status' => $log['halal_status'],
                        'active' => true,
                        'source' => 'local',
                    ]
                );

                $recordedAt = Carbon::createFromTimestampMs($log['scanned_at']);

                $scanHistory = new ScanHistory([
                    'user_id' => $userId,
                    'scannable_type' => ProductModel::class,
                    'scannable_id' => $product->getKey(),
                    'product_name' => $log['product_name'] ?? $product->nama_product,
                    'product_image' => $product->image ?? null,
                    'barcode' => $log['barcode'],
                    'halal_status' => $log['halal_status'],
                    'scan_method' => 'barcode',
                    'source' => 'local',
                    'confidence_score' => null,
                    'nutrition_snapshot' => [
                        'ai_analysis' => $log['ai_analysis'] ?? null,
                        'synced_from' => 'offline_batch',
                    ],
                    'is_synced' => true,
                ]);

                $scanHistory->created_at = $recordedAt;
                $scanHistory->updated_at = $recordedAt;
                $scanHistory->save();

                $createdCount++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sync berhasil',
            'count' => $createdCount,
        ]);
    }

    public function syncHealthLogs(Request $request)
    {
        $request->validate([
            'logs' => 'required|array|max:100',
            'logs.*.log_type' => 'required|in:weight,blood_pressure,glucose,blood_sugar,cholesterol',
            'logs.*.value1' => 'required|numeric',
            'logs.*.value2' => 'nullable|numeric',
            'logs.*.unit' => 'required|string|max:50',
            'logs.*.notes' => 'nullable|string',
            'logs.*.recorded_at' => 'required|integer',
        ]);

        $userId = $request->user()->id_user;
        $createdCount = 0;

        DB::transaction(function () use ($request, $userId, &$createdCount) {
            foreach ($request->input('logs', []) as $log) {
                $metricType = $this->mapMetricType($log['log_type']);
                $recordedAt = Carbon::createFromTimestampMs($log['recorded_at']);

                $value = $metricType === 'blood_pressure'
                    ? $log['value1'] . '/' . ($log['value2'] ?? '')
                    : ((string) $log['value1']) . ' ' . $log['unit'];

                $healthTracking = new HealthTracking([
                    'id_user' => $userId,
                    'metric_type' => $metricType,
                    'value' => trim($value),
                    'notes' => $log['notes'] ?? null,
                ]);

                $healthTracking->recorded_at = $recordedAt;
                $healthTracking->created_at = $recordedAt;
                $healthTracking->updated_at = $recordedAt;
                $healthTracking->save();

                $createdCount++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Health logs sync berhasil',
            'count' => $createdCount,
        ]);
    }

    private function mapMetricType(string $logType): string
    {
        return match ($logType) {
            'glucose' => 'blood_sugar',
            default => $logType,
        };
    }
}
