<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanHistory;
use App\Models\ScanModel;
use App\Models\Notification;
use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;

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
        $query = ScanHistory::byUser($request->user()->id_user)
            ->with('scannable')
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
     * Record a scan (called after successful product scan)
     */
    public function recordScan(Request $request)
    {
        $validated = $request->validate([
            'scannable_type' => 'required|string',
            'scannable_id' => 'required|integer',
            'product_name' => 'required|string',
            'product_image' => 'nullable|string',
            'barcode' => 'nullable|string',
            'halal_status' => 'required|string',
            'scan_method' => 'required|in:barcode,qr_code,text_search,photo',
            'source' => 'required|in:local,open_food_facts,umkm,street_food',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'confidence_score' => 'nullable|integer',
            'nutrition_snapshot' => 'nullable|array',
        ]);

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
        $scanHistory = ScanHistory::byUser($request->user()->id)->findOrFail($id);
        $scanHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'History deleted'
        ]);
    }
}
