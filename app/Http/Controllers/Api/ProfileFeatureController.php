<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\GeminiService;

class ProfileFeatureController extends Controller
{
    /**
     * Get user achievements based on total scans and streaks
     */
    public function getAchievements(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Logic 1: Check total scans
        $totalScans = DB::table('scan_histories')->where('user_id', $user->id_user)->count();
        
        // Logic 2: Check streaks
        $streak = $user->longest_streak ?? 1; // Used longest streak to retain achievements

        $this->checkAndUnlockAchievement($user->id_user, 'First Scan', '1 Scan', $totalScans >= 1, 'badge_first_scan.png');
        $this->checkAndUnlockAchievement($user->id_user, 'Scanner Novice', '10 Scans', $totalScans >= 10, 'badge_10_scans.png');
        $this->checkAndUnlockAchievement($user->id_user, 'Scanner Pro', '50 Scans', $totalScans >= 50, 'badge_50_scans.png');
        $this->checkAndUnlockAchievement($user->id_user, 'Halal Expert', '100 Scans', $totalScans >= 100, 'badge_100_scans.png');

        $this->checkAndUnlockAchievement($user->id_user, '3 Day Streak', '3 Days Active', $streak >= 3, 'badge_3_streak.png');
        $this->checkAndUnlockAchievement($user->id_user, '7 Day Streak', '7 Days Active', $streak >= 7, 'badge_7_streak.png');
        $this->checkAndUnlockAchievement($user->id_user, '30 Day Streak', '1 Month Active', $streak >= 30, 'badge_30_streak.png');

        $achievements = DB::table('user_achievements')
            ->where('user_id', $user->id_user)
            ->get();

        return response()->json([
            'success' => true,
            'achievements' => $achievements,
            'stats' => [
                'total_scans' => $totalScans,
                'current_streak' => $user->current_streak ?? 0,
                'longest_streak' => $user->longest_streak ?? 0,
            ]
        ]);
    }

    private function checkAndUnlockAchievement($userId, $badgeName, $criteria, $isMet, $icon = null)
    {
        if ($isMet) {
            $exists = DB::table('user_achievements')
                ->where('user_id', $userId)
                ->where('badge_name', $badgeName)
                ->exists();

            if (!$exists) {
                DB::table('user_achievements')->insert([
                    'user_id' => $userId,
                    'badge_name' => $badgeName,
                    'badge_icon_url' => $icon,
                    'unlocked_at' => Carbon::now(),
                    'is_notified' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Export Monthly Report to PDF
     */
    public function exportMonthlyReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->format('Y-m')); // e.g. "2026-02"
        
        // 1. Fetch scan history for that month
        $scans = DB::table('scan_histories')
            ->where('user_id', $user->id_user)
            ->where('created_at', 'like', $month . '%')
            ->get();

        $totalScans = $scans->count();
        $halalCount = $scans->where('halal_status', 'halal')->count();
        $haramCount = $scans->where('halal_status', 'haram')->count();
        $syubhatCount = $scans->where('halal_status', 'syubhat')->count();

        $stats = [
            'total_scans' => $totalScans,
            'halal_count' => $halalCount,
            'haram_count' => $haramCount,
            'syubhat_count' => $syubhatCount,
        ];

        // 2. AI Summary using Gemini
        $prompt = "Tuliskan 1 paragraf ringkasan kesehatan untuk pengguna ({$user->full_name}) di bulan {$month}. Statistik scan: Total ({$totalScans}), Halal ({$halalCount}), Haram ({$haramCount}), Syubhat ({$syubhatCount}). Berikan pujian jika dominan halal, dan saran jika banyak haram/syubhat.";
        
        $gemini = app(GeminiService::class);
        $aiSummary = "Laporan Halalytics untuk " . $user->full_name;
        
        try {
            $geminiResult = $gemini->generateText($prompt);
            if (is_array($geminiResult)) {
                $aiSummary = json_encode($geminiResult);
            } else if (is_string($geminiResult)) {
                $aiSummary = $geminiResult;
            }
        } catch (\Exception $e) {
            $aiSummary = "Laporan Halalytics Anda menunjukkan Anda memindai {$totalScans} produk bulan ini.";
        }

        // 3. Render HTML and generate PDF
        $html = "
            <html>
                <head><style>body { font-family: sans-serif; }</style></head>
                <body>
                    <h1>Laporan Halalytics - {$month}</h1>
                    <p><strong>Nama:</strong> {$user->full_name}</p>
                    <hr>
                    <h2>Statistik Pindaian</h2>
                    <ul>
                        <li>Total Produk Dipindai: {$totalScans}</li>
                        <li>Produk Halal: {$halalCount}</li>
                        <li>Produk Syubhat: {$syubhatCount}</li>
                        <li>Produk Haram: {$haramCount}</li>
                    </ul>
                    <h2>Analisis AI</h2>
                    <p>{$aiSummary}</p>
                </body>
            </html>
        ";

        try {
            if (class_exists(Pdf::class)) {
                $pdf = Pdf::loadHTML($html);
                $fileName = "report_{$user->id_user}_{$month}.pdf";
                
                // Save locally to storage
                $path = storage_path("app/public/reports/{$fileName}");
                
                // Ensure directory exists
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }
                
                $pdf->save($path);
                $fileUrl = url("storage/reports/{$fileName}");
            } else {
                return response()->json(['success' => false, 'message' => 'DomPDF not installed'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }

        // 4. Save to DB
        $reportId = DB::table('monthly_reports')->insertGetId([
            'user_id' => $user->id_user,
            'month' => $month,
            'file_url' => $fileUrl,
            'scan_stats' => json_encode($stats),
            'ai_summary' => $aiSummary,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat',
            'report_url' => $fileUrl,
            'month' => $month
        ]);
    }
}
