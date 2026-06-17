<?php

namespace App\Http\Controllers\Api;

use App\Models\ScanModel;
use Carbon\Carbon;

class DuplicateScanDetector
{
    private const DUPLICATE_WINDOW_MINUTES = 10;

    public static function checkAndMerge($userId, $productId, $barcode)
    {
        $recentScan = ScanModel::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('tanggal_scan', '>=', now()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))
            ->latest('tanggal_scan')
            ->first();

        if ($recentScan) {
            return [
                'is_duplicate' => true,
                'message' => 'Produk ini sudah dipindai ' . $recentScan->tanggal_scan->diffForHumans(),
                'scan_id' => $recentScan->id,
                'previous_scan_at' => $recentScan->tanggal_scan->toIso8601String(),
            ];
        }

        $newScan = ScanModel::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'tanggal_scan' => now(),
            'barcode' => $barcode,
        ]);

        return [
            'is_duplicate' => false,
            'scan_id' => $newScan->id,
            'created_at' => $newScan->tanggal_scan->toIso8601String(),
        ];
    }
}
