<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanHistory;
use App\Models\ScanModel;
use App\Models\Notification;
use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScanHistoryController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseRealtimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get user scan history
     */
    public function index(Request $request)
    {
        if (!Schema::hasTable('scan_histories')) {
            $legacyQuery = ScanModel::query()
                ->where('user_id', $request->user()->id_user)
                ->orderByDesc(DB::raw('COALESCE(tanggal_scan, created_at)'));

            if ($request->filled('period')) {
                switch ($request->period) {
                    case 'today':
                        $legacyQuery->whereDate('created_at', today());
                        break;
                    case 'week':
                        $legacyQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $legacyQuery->whereMonth('created_at', now()->month);
                        break;
                }
            }

            // Legacy scans are local/manual. For unsupported source filters, return empty set.
            if ($request->filled('source')) {
                $source = strtolower((string) $request->source);
                if (!in_array($source, ['local', 'manual', 'unknown', 'bpom'], true)) {
                    $legacyQuery->whereRaw('1 = 0');
                }
            }

            $legacy = $legacyQuery->paginate(20);
            $legacy->getCollection()->transform(function (ScanModel $scan) {
                return $this->transformLegacyScan($scan);
            });

            $baseStatsQuery = ScanModel::query()->where('user_id', $request->user()->id_user);
            return response()->json([
                'success' => true,
                'data' => $legacy,
                'stats' => [
                    'total_scans' => (clone $baseStatsQuery)->count(),
                    'today_scans' => (clone $baseStatsQuery)->whereDate('created_at', today())->count(),
                    'week_scans' => (clone $baseStatsQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'halal_count' => (clone $baseStatsQuery)->where('status_halal', 'halal')->count(),
                ],
                'message' => 'Menampilkan riwayat scan dari tabel legacy.',
            ]);
        }

        $query = ScanHistory::byUser($request->user()->id_user)
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->today();
                    break;
                case 'week':
                    $query->thisWeek();
                    break;
                case 'month':
                    $query->thisMonth();
                    break;
            }
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $histories = $query->paginate(20);

        // Statistics
        $stats = [
            'total_scans' => ScanHistory::byUser($request->user()->id_user)->count(),
            'today_scans' => ScanHistory::byUser($request->user()->id_user)->today()->count(),
            'week_scans' => ScanHistory::byUser($request->user()->id_user)->thisWeek()->count(),
            'halal_count' => ScanHistory::byUser($request->user()->id_user)
                ->where('halal_status', 'halal')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $histories,
            'stats' => $stats
        ]);
    }

    /**
     * Get single scan history detail
     */
    public function show($id, Request $request)
    {
        if (!Schema::hasTable('scan_histories')) {
            $legacy = ScanModel::query()
                ->where('user_id', $request->user()->id_user)
                ->where('id_scan', $id)
                ->first();

            if (!$legacy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Riwayat scan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->transformLegacyScan($legacy),
                'message' => 'Detail riwayat scan dari tabel legacy.',
            ]);
        }

        $scanHistory = ScanHistory::byUser($request->user()->id_user)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $scanHistory,
        ]);
    }

    /**
     * Record a scan (called after successful product scan)
     */
    public function recordScan(Request $request)
    {
        if (!Schema::hasTable('scan_histories')) {
            $legacyValidated = $request->validate([
                'product_name' => 'required|string',
                'barcode' => 'nullable|string',
                'halal_status' => 'required|string',
                'nutrition_snapshot' => 'nullable|array',
            ]);

            $scan = ScanModel::create([
                'user_id' => $request->user()->id_user,
                'product_id' => null,
                'nama_produk' => $legacyValidated['product_name'],
                'barcode' => $legacyValidated['barcode'] ?? null,
                'kategori' => $legacyValidated['halal_status'],
                'status_halal' => $legacyValidated['halal_status'],
                'status_kesehatan' => 'sehat',
                'tanggal_scan' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scan recorded successfully (legacy)',
                'data' => $this->transformLegacyScan($scan),
            ]);
        }

        $validated = $request->validate([
            'scannable_type' => 'required|string',
            'scannable_id' => 'required|integer',
            'product_name' => 'required|string',
            'product_image' => 'nullable|string',
            'barcode' => 'nullable|string',
            'halal_status' => 'required|string',
            'scan_method' => 'required|in:barcode,qr_code,text_search,photo',
            'source' => 'required|in:local,open_food_facts,open_beauty_facts,openfda,umkm,street_food,manual,bpom',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'confidence_score' => 'nullable|integer',
            'nutrition_snapshot' => 'nullable|array',
        ]);

        $validated['scannable_type'] = $this->normalizeScannableType(
            $validated['scannable_type']
        );
        $validated['source'] = $this->normalizeSource($validated['source']);

        $scanHistory = ScanHistory::create([
            ...$validated,
            'user_id' => $request->user()->id_user,
        ]);

        // Sync to Firebase Realtime Database
        $this->firebaseService->syncScanHistory($scanHistory);

        // Record to legacy scans table for Admin Dashboard compatibility
        ScanModel::create([
            'user_id' => $request->user()->id_user,
            'product_id' => $validated['scannable_type'] === 'product' ? $validated['scannable_id'] : null,
            'nama_produk' => $validated['product_name'],
            'barcode' => $validated['barcode'],
            'kategori' => $validated['halal_status'],
            'status_halal' => $validated['halal_status'],
            'status_kesehatan' => 'sehat',
            'tanggal_scan' => now(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $request->user()->id_user,
            'title' => '✅ Scan Berhasil',
            'message' => "Produk '{$scanHistory->product_name}' berhasil dianalisis",
            'type' => 'scan',
            'related_product_id' => $scanHistory->scannable_type === 'App\Models\ProductModel' ? $scanHistory->scannable_id : null,
        ]);

        // Create Admin Notification for Real-time Visibility
        try {
            \App\Http\Controllers\Admin\AdminNotificationController::createNotification(
                'scan',
                'Scan Produk Baru',
                "User " . $request->user()->username . " melakukan scan produk: " . $scanHistory->product_name,
                ['scan_id' => $scanHistory->id, 'product_name' => $scanHistory->product_name]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create admin notification for scan: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Scan recorded successfully',
            'data' => $scanHistory
        ]);
    }

    /**
     * Delete scan history
     */
    public function destroy($id, Request $request)
    {
        if (!Schema::hasTable('scan_histories')) {
            $legacy = ScanModel::query()
                ->where('user_id', $request->user()->id_user)
                ->where('id_scan', $id)
                ->firstOrFail();
            $legacy->delete();

            return response()->json([
                'success' => true,
                'message' => 'History deleted (legacy)'
            ]);
        }

        $scanHistory = ScanHistory::byUser($request->user()->id_user)->findOrFail($id);
        $scanHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'History deleted'
        ]);
    }

    private function normalizeScannableType(string $value): string
    {
        $normalized = strtolower(trim($value));

        return match ($normalized) {
            'product', 'app\\models\\productmodel', 'app/models/productmodel' => \App\Models\ProductModel::class,
            'bpom', 'app\\models\\bpomdata', 'app/models/bpomdata' => \App\Models\BpomData::class,
            'manual', 'external', 'unknown' => 'manual',
            default => $value,
        };
    }

    private function normalizeSource(string $source): string
    {
        return match (strtolower(trim($source))) {
            'open_beauty_facts' => 'open_food_facts',
            'openfda' => 'local',
            'manual', 'bpom' => 'local',
            default => $source,
        };
    }

    private function transformLegacyScan(ScanModel $scan): array
    {
        return [
            'id' => (int) $scan->id_scan,
            'product_name' => $scan->nama_produk,
            'product_image' => null,
            'barcode' => $scan->barcode,
            'halal_status' => $scan->status_halal ?: 'unknown',
            'source' => 'local',
            'scan_method' => 'legacy',
            'created_at' => optional($scan->tanggal_scan ?: $scan->created_at)?->toIso8601String(),
        ];
    }
}
