<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\OcrScanHistory;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(private readonly GamificationService $gamification)
    {
    }

    public function dailyMission(Request $request)
    {
        $userId = (int) $request->user()->id_user;
        $today = now()->toDateString();
        $locationCompleted = $this->isManualMissionCompleted($userId, $today, 'location');

        $missions = [
            [
                'id' => 'scan',
                'title' => 'Scan 1 produk baru',
                'description' => 'Pakai Offline OCR di supermarket',
                'is_completed' => OcrScanHistory::query()
                    ->where('user_id', $userId)
                    ->whereDate('scanned_at', $today)
                    ->exists(),
                'points_reward' => 15,
                'icon_type' => 'scan',
            ],
            [
                'id' => 'nutrition',
                'title' => 'Catat makan hari ini',
                'description' => 'Foto makananmu dan hitung kalorinya',
                'is_completed' => DailyLog::query()
                    ->where('user_id', $userId)
                    ->where('logged_at', $today)
                    ->exists(),
                'points_reward' => 20,
                'icon_type' => 'nutrition',
            ],
            [
                'id' => 'location',
                'title' => 'Temukan klinik terdekat',
                'description' => 'Gunakan AR Finder untuk navigasi visual',
                'is_completed' => $locationCompleted,
                'points_reward' => 10,
                'icon_type' => 'location',
            ],
        ];

        $completedCount = collect($missions)->where('is_completed', true)->count();
        $pointsEarnedToday = (int) collect($missions)
            ->where('is_completed', true)
            ->sum('points_reward');

        return $this->successResponse([
            'missions' => $missions,
            'points_earned_today' => $pointsEarnedToday,
            'completed_count' => $completedCount,
            'total_count' => count($missions),
        ], 'Dashboard misi harian berhasil diambil.');
    }

    public function completeMission(Request $request)
    {
        $validated = $request->validate([
            'mission_id' => 'required|in:location',
        ]);

        $userId = (int) $request->user()->id_user;
        $today = now()->toDateString();
        $cacheKey = $this->manualMissionCacheKey($userId, $today, $validated['mission_id']);

        if (! Cache::get($cacheKey)) {
            Cache::put($cacheKey, true, now()->endOfDay());

            if ($validated['mission_id'] === 'location') {
                $this->gamification->addPoints($userId, 10, 'Menyelesaikan misi harian AR Finder', 'daily_mission', null);
            }
        }

        return $this->successResponse([
            'mission_id' => $validated['mission_id'],
            'is_completed' => true,
        ], 'Misi harian berhasil ditandai selesai.');
    }

    private function isManualMissionCompleted(int $userId, string $date, string $missionId): bool
    {
        return (bool) Cache::get($this->manualMissionCacheKey($userId, $date, $missionId), false);
    }

    private function manualMissionCacheKey(int $userId, string $date, string $missionId): string
    {
        return "daily_mission:{$date}:{$userId}:{$missionId}";
    }
}
