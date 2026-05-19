<?php

namespace App\Services\AI;

use App\Models\UserFoodBehavior;
use Carbon\Carbon;

class UserBehaviorAnalyzer
{
    public function recordScan(int $userId, array $productMeta): void
    {
        if (! \Schema::hasTable('user_food_behavior')) {
            return;
        }

        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $category = $productMeta['category'] ?? 'makanan';
        $highSugar = ($productMeta['sugar_risk'] ?? '') === 'tinggi';
        $highSodium = ($productMeta['sodium_risk'] ?? '') === 'tinggi';

        $row = UserFoodBehavior::query()->firstOrNew([
            'user_id' => $userId,
            'product_category' => $category,
            'week_start' => $weekStart,
        ]);

        $row->scan_count = ($row->scan_count ?? 0) + 1;
        if ($highSugar) {
            $row->high_sugar_count = ($row->high_sugar_count ?? 0) + 1;
        }
        if ($highSodium) {
            $row->high_sodium_count = ($row->high_sodium_count ?? 0) + 1;
        }
        $row->last_scan = now();
        $row->consumption_risk = $this->riskFromCounts($row->high_sugar_count, $row->scan_count);
        $row->save();
    }

    public function weeklySummary(int $userId): array
    {
        if (! \Schema::hasTable('user_food_behavior')) {
            return ['scan_count' => 0, 'high_sugar_scan_count' => 0, 'message' => ''];
        }

        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $rows = UserFoodBehavior::query()
            ->where('user_id', $userId)
            ->where('week_start', $weekStart)
            ->get();

        $totalScans = $rows->sum('scan_count');
        $highSugar = $rows->sum('high_sugar_count');

        $message = '';
        if ($highSugar >= 10) {
            $message = 'Pola konsumsi minggu ini: banyak produk tinggi gula. Pertimbangkan alternatif lebih sehat.';
        } elseif ($totalScans >= 20) {
            $message = 'Anda cukup aktif memindai produk minggu ini — pertahankan kebiasaan membaca label.';
        }

        return [
            'weekly_scan_summary' => "{$totalScans} scan minggu ini",
            'high_sugar_scan_count' => $highSugar,
            'consumption_pattern_message' => $message,
        ];
    }

    private function riskFromCounts(int $highSugar, int $scans): string
    {
        if ($highSugar >= 10 || $scans >= 30) {
            return 'critical';
        }
        if ($highSugar >= 5 || $scans >= 15) {
            return 'high';
        }
        if ($highSugar >= 2) {
            return 'moderate';
        }

        return 'low';
    }
}
