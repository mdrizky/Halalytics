<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthTracking;
use App\Models\ProductModel;
use App\Models\ScanHistory;
use App\Models\ScanModel;
use App\Models\SyncConflict;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $conflicts = [];

        if (!Schema::hasTable('scan_histories')) {
            DB::transaction(function () use ($request, $userId, &$createdCount) {
                foreach ($request->input('logs', []) as $log) {
                    $recordedAt = Carbon::createFromTimestampMs((int) $log['scanned_at']);
                    ScanModel::create([
                        'user_id' => $userId,
                        'product_id' => null,
                        'nama_produk' => $log['product_name'] ?? 'Unknown Product',
                        'barcode' => $log['barcode'],
                        'kategori' => $log['halal_status'],
                        'status_halal' => $log['halal_status'],
                        'status_kesehatan' => 'sehat',
                        'tanggal_scan' => $recordedAt,
                    ]);
                    $createdCount++;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Sync berhasil (legacy scans)',
                'count' => $createdCount,
            ]);
        }

        DB::transaction(function () use ($request, $userId, &$createdCount, &$conflicts) {
            foreach ($request->input('logs', []) as $log) {
                $recordedAt = Carbon::createFromTimestampMs((int) $log['scanned_at']);
                
                $product = ProductModel::firstOrCreate(
                    ['barcode' => $log['barcode']],
                    [
                        'nama_product' => $log['product_name'] ?? 'Unknown Product',
                        'status' => $log['halal_status'],
                        'active' => true,
                        'source' => 'local',
                    ]
                );

                $existingScan = ScanHistory::where('user_id', $userId)
                    ->where('barcode', $log['barcode'])
                    ->where('created_at', '>=', $recordedAt->subMinutes(10))
                    ->where('created_at', '<=', $recordedAt->addMinutes(10))
                    ->first();

                if ($existingScan) {
                    $conflict = $this->handleDuplicateScanConflict(
                        $userId,
                        $existingScan,
                        $log,
                        $recordedAt
                    );
                    $conflicts[] = $conflict;
                } else {
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
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sync berhasil',
            'count' => $createdCount,
            'conflicts' => $conflicts,
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
        $conflicts = [];

        DB::transaction(function () use ($request, $userId, &$createdCount, &$conflicts) {
            foreach ($request->input('logs', []) as $log) {
                $metricType = $this->mapMetricType($log['log_type']);
                $recordedAt = Carbon::createFromTimestampMs($log['recorded_at']);

                $existingMetric = HealthTracking::where('id_user', $userId)
                    ->where('metric_type', $metricType)
                    ->where('created_at', '>=', $recordedAt->subMinutes(5))
                    ->where('created_at', '<=', $recordedAt->addMinutes(5))
                    ->first();

                if ($existingMetric) {
                    $conflict = $this->handleHealthConflict(
                        $userId,
                        $existingMetric,
                        $log,
                        $recordedAt
                    );
                    $conflicts[] = $conflict;
                } else {
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
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Health logs sync berhasil',
            'count' => $createdCount,
            'conflicts' => $conflicts,
        ]);
    }

    private function handleDuplicateScanConflict($userId, $existing, $mobileData, $mobileTimestamp)
    {
        $serverData = $existing->toArray();
        $serverTimestamp = $existing->created_at;

        if ($mobileTimestamp->greaterThan($serverTimestamp)) {
            $existing->update([
                'halal_status' => $mobileData['halal_status'],
                'product_name' => $mobileData['product_name'] ?? $existing->product_name,
            ]);
            $winnerSource = 'mobile';
        } else {
            $winnerSource = 'server';
        }

        $conflict = SyncConflict::create([
            'user_id' => $userId,
            'resource_type' => 'scan_history',
            'resource_id' => $existing->id,
            'mobile_data' => $mobileData,
            'server_data' => $serverData,
            'mobile_timestamp' => $mobileTimestamp,
            'server_timestamp' => $serverTimestamp,
            'resolution_strategy' => 'last-write-wins',
            'resolution_result' => [
                'winner' => $winnerSource,
                'action' => 'merged',
            ],
            'resolved_at' => now(),
        ]);

        return [
            'conflict_id' => $conflict->id,
            'resource_type' => 'scan_history',
            'resolution' => $winnerSource === 'mobile' ? 'server_updated' : 'mobile_rejected',
            'server_version' => $serverData,
        ];
    }

    private function handleHealthConflict($userId, $existing, $mobileData, $mobileTimestamp)
    {
        $serverData = $existing->toArray();
        $serverTimestamp = $existing->created_at;

        if ($mobileTimestamp->greaterThan($serverTimestamp)) {
            $mobileValue = $mobileData['value1'] . ' ' . $mobileData['unit'];
            $existing->update([
                'value' => $mobileValue,
                'notes' => $mobileData['notes'] ?? $existing->notes,
            ]);
            $winnerSource = 'mobile';
        } else {
            $winnerSource = 'server';
        }

        $conflict = SyncConflict::create([
            'user_id' => $userId,
            'resource_type' => 'health_tracking',
            'resource_id' => $existing->id,
            'mobile_data' => $mobileData,
            'server_data' => $serverData,
            'mobile_timestamp' => $mobileTimestamp,
            'server_timestamp' => $serverTimestamp,
            'resolution_strategy' => 'last-write-wins',
            'resolution_result' => [
                'winner' => $winnerSource,
                'action' => 'merged',
            ],
            'resolved_at' => now(),
        ]);

        return [
            'conflict_id' => $conflict->id,
            'resource_type' => 'health_tracking',
            'resolution' => $winnerSource === 'mobile' ? 'server_updated' : 'mobile_rejected',
            'server_version' => $serverData,
        ];
    }

    private function mapMetricType(string $logType): string
    {
        return match ($logType) {
            'glucose' => 'blood_sugar',
            default => $logType,
        };
    }
}
