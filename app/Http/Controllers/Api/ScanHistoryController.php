<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanHistory;
use App\Models\ScanIngredient;
use App\Models\DailyNutrition;
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

    public function index(Request $request)
    {
        if (!Schema::hasTable('scan_histories')) {
            return $this->legacyIndex($request);
        }

        $query = ScanHistory::byUser($request->user()->id_user)
            ->orderBy('created_at', 'desc');

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

        if ($request->filled('halal_status')) {
            $query->where('halal_status', $request->halal_status);
        }

        $histories = $query->paginate(20);

        $scanQuery = ScanHistory::byUser($request->user()->id_user);
        $stats = [
            'total_scans' => (clone $scanQuery)->count(),
            'today_scans' => (clone $scanQuery)->today()->count(),
            'week_scans' => (clone $scanQuery)->thisWeek()->count(),
            'halal_count' => (clone $scanQuery)->where('halal_status', 'halal')->count(),
            'syubhat_count' => (clone $scanQuery)->where('halal_status', 'syubhat')->count(),
            'haram_count' => (clone $scanQuery)->where('halal_status', 'haram')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $histories,
            'stats' => $stats
        ]);
    }

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
            ->with('ingredients')
            ->findOrFail($id);

        // Get daily nutrition context
        $dailyNutrition = DailyNutrition::byUser($request->user()->id_user)
            ->where('scan_date', $scanHistory->scan_date ?? $scanHistory->created_at->toDateString())
            ->first();

        return response()->json([
            'success' => true,
            'data' => $scanHistory,
            'daily_nutrition' => $dailyNutrition,
        ]);
    }

    public function recordScan(Request $request)
    {
        if (!Schema::hasTable('scan_histories')) {
            return $this->legacyRecordScan($request);
        }

        $validated = $request->validate([
            'scannable_type' => 'required|string',
            'scannable_id' => 'required|integer',
            'product_name' => 'required|string',
            'product_image' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'barcode' => 'nullable|string',
            'halal_status' => 'required|string',
            'health_score' => 'nullable|integer|min:0|max:100',
            'calories' => 'nullable|numeric',
            'protein' => 'nullable|numeric',
            'carbs' => 'nullable|numeric',
            'fat' => 'nullable|numeric',
            'fiber' => 'nullable|numeric',
            'sugar' => 'nullable|numeric',
            'sodium' => 'nullable|numeric',
            'nova_group' => 'nullable|string',
            'nutri_score' => 'nullable|string',
            'ai_recommendation' => 'nullable|string',
            'short_term_effects' => 'nullable|array',
            'long_term_effects' => 'nullable|array',
            'scan_date' => 'nullable|date',
            'scan_method' => 'required|in:barcode,qr_code,text_search,photo',
            'source' => 'required|in:local,open_food_facts,open_beauty_facts,openfda,umkm,street_food,manual,bpom',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'confidence_score' => 'nullable|integer',
            'nutrition_snapshot' => 'nullable|array',
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_name' => 'required_with:ingredients|string',
            'ingredients.*.status' => 'required_with:ingredients|in:halal,syubhat,haram',
            'ingredients.*.warning' => 'nullable|string',
            'ingredients.*.e_code' => 'nullable|string',
            'ingredients.*.category' => 'nullable|string',
            'ingredients.*.source_type' => 'nullable|string',
        ]);

        $validated['manufacturer'] = $validated['manufacturer'] ?? null;
        $validated['scannable_type'] = $this->normalizeScannableType(
            $validated['scannable_type']
        );
        $validated['source'] = $this->normalizeSource($validated['source']);

        $ingredients = $validated['ingredients'] ?? [];
        $nutritionFields = ['calories','protein','carbs','fat','fiber','sugar','sodium','nova_group','nutri_score','ai_recommendation','short_term_effects','long_term_effects','scan_date','scan_method','source','latitude','longitude','confidence_score','nutrition_snapshot','health_score'];
        $scanData = [
            'scannable_type' => $validated['scannable_type'],
            'scannable_id' => $validated['scannable_id'],
            'product_name' => $validated['product_name'],
            'product_image' => $validated['product_image'] ?? null,
            'barcode' => $validated['barcode'] ?? null,
            'halal_status' => $validated['halal_status'],
            'user_id' => $request->user()->id_user,
        ];
        foreach ($nutritionFields as $field) {
            if (array_key_exists($field, $validated)) {
                $scanData[$field] = $validated[$field];
            }
        }
        unset($validated['ingredients']);

        $scanHistory = ScanHistory::create($scanData);

        // Save ingredients
        $this->saveIngredients($scanHistory->id, $ingredients);

        // Update daily nutrition
        $this->updateDailyNutrition($request->user()->id_user, $scanHistory, $validated);

        // Increment user's total scan count
        $request->user()->increment('total_scans');

        // Sync to Firebase
        $this->firebaseService->syncScanHistory($scanHistory);

        // Legacy sync
        ScanModel::create([
            'user_id' => $request->user()->id_user,
            'product_id' => $validated['scannable_type'] === \App\Models\ProductModel::class ? $validated['scannable_id'] : null,
            'nama_produk' => $validated['product_name'],
            'barcode' => $validated['barcode'],
            'kategori' => $validated['halal_status'],
            'status_halal' => $validated['halal_status'],
            'status_kesehatan' => 'sehat',
            'tanggal_scan' => now(),
        ]);

        // Notification
        Notification::create([
            'user_id' => $request->user()->id_user,
            'title' => '✅ Scan Berhasil',
            'message' => "Produk '{$scanHistory->product_name}' berhasil dianalisis",
            'type' => 'scan',
            'related_product_id' => $scanHistory->scannable_type === 'App\Models\ProductModel' ? $scanHistory->scannable_id : null,
        ]);

        try {
            \App\Http\Controllers\Admin\AdminNotificationController::createNotification(
                'scan',
                'Scan Produk Baru',
                "User " . $request->user()->username . " melakukan scan produk: " . $scanHistory->product_name,
                ['scan_id' => $scanHistory->id, 'product_name' => $scanHistory->product_name]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create admin notification: ' . $e->getMessage());
        }

        \App\Services\ActivityLogger::scan(
            $scanHistory->product_name,
            $scanHistory->barcode ?? '-',
            $scanHistory->halal_status,
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Scan recorded successfully',
            'data' => $scanHistory->load('ingredients')
        ]);
    }

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
        // ingredients cascade delete via FK
        $scanHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'History deleted'
        ]);
    }

    private function saveIngredients(int $scanHistoryId, array $ingredients): void
    {
        if (empty($ingredients)) {
            return;
        }

        $data = array_map(fn($item) => [
            'scan_history_id' => $scanHistoryId,
            'ingredient_name' => $item['ingredient_name'],
            'status' => $item['status'],
            'warning' => $item['warning'] ?? null,
            'e_code' => $item['e_code'] ?? null,
            'category' => $item['category'] ?? null,
            'source_type' => $item['source_type'] ?? 'ingredient',
            'created_at' => now(),
            'updated_at' => now(),
        ], $ingredients);

        ScanIngredient::insert($data);
    }

    private function updateDailyNutrition(int $userId, ScanHistory $scan, array $data): void
    {
        $scanDate = $data['scan_date'] ?? today()->toDateString();

        DailyNutrition::updateOrCreate(
            ['user_id' => $userId, 'scan_date' => $scanDate],
            [
                'total_calories' => DB::raw("COALESCE(total_calories, 0) + " . ($data['calories'] ?? 0)),
                'total_protein'  => DB::raw("COALESCE(total_protein, 0) + " . ($data['protein'] ?? 0)),
                'total_carbs'    => DB::raw("COALESCE(total_carbs, 0) + " . ($data['carbs'] ?? 0)),
                'total_fat'      => DB::raw("COALESCE(total_fat, 0) + " . ($data['fat'] ?? 0)),
                'total_fiber'    => DB::raw("COALESCE(total_fiber, 0) + " . ($data['fiber'] ?? 0)),
                'total_sugar'    => DB::raw("COALESCE(total_sugar, 0) + " . ($data['sugar'] ?? 0)),
                'total_sodium'   => DB::raw("COALESCE(total_sodium, 0) + " . ($data['sodium'] ?? 0)),
                'total_scans'    => DB::raw("COALESCE(total_scans, 0) + 1"),
                'total_halal'    => DB::raw("COALESCE(total_halal, 0) + " . (in_array($scan->halal_status, ['halal', 'halal_sertifikat']) ? 1 : 0)),
                'total_syubhat'  => DB::raw("COALESCE(total_syubhat, 0) + " . ($scan->halal_status === 'syubhat' ? 1 : 0)),
                'total_haram'    => DB::raw("COALESCE(total_haram, 0) + " . ($scan->halal_status === 'haram' ? 1 : 0)),
            ]
        );

        // Recalculate average health score
        $avgScore = ScanHistory::byUser($userId)
            ->whereDate('created_at', $scanDate)
            ->whereNotNull('health_score')
            ->avg('health_score');

        DailyNutrition::where('user_id', $userId)
            ->where('scan_date', $scanDate)
            ->update(['health_score_avg' => $avgScore ? round($avgScore, 2) : null]);
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
        $s = strtolower(trim($source));

        return match ($s) {
            'open_beauty_facts', 'openbeautyfacts', 'obf',
            'openfda', 'fda',
            'bpom' => 'local',
            'open_food_facts', 'openfoodfacts', 'off' => 'open_food_facts',
            'manual', 'text_search', 'umkm', 'street_food' => 'local',
            default => $s !== '' ? $s : 'local',
        };
    }

    private function legacyIndex(Request $request)
    {
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

        if ($request->filled('source')) {
            $source = strtolower((string) $request->source);
            if (!in_array($source, ['local', 'manual', 'unknown', 'bpom'], true)) {
                $legacyQuery->whereRaw('1 = 0');
            }
        }

        $legacy = $legacyQuery->paginate(20);
        $legacy->getCollection()->transform(fn(ScanModel $scan) => $this->transformLegacyScan($scan));

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

    private function legacyRecordScan(Request $request)
    {
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
