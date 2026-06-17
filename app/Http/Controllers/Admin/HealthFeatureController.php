<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthFeatureController extends Controller
{
    /**
     * Health Feature registry — defines all available AI health features.
     */
    private function getFeatureRegistry(): array
    {
        $toggles = Cache::get('health_feature_toggles', []);

        return [
            [
                'key' => 'calorie_counter',
                'name' => 'Penghitung Kalori',
                'description' => 'Tracking kalori dan makronutrien harian.',
                'icon' => 'local_fire_department',
                'color' => '#FF6D00',
                'status' => 'active',
                'enabled' => $toggles['calorie_counter'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'water_tracker',
                'name' => 'Pelacak Air Minum',
                'description' => 'Pengingat dan pelacak hidrasi harian.',
                'icon' => 'water_drop',
                'color' => '#00B0FF',
                'status' => 'active',
                'enabled' => $toggles['water_tracker'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'food_scanner',
                'name' => 'AI Food Scanner',
                'description' => 'Foto makanan → estimasi gizi otomatis.',
                'icon' => 'camera_alt',
                'color' => '#00C853',
                'status' => 'active',
                'enabled' => $toggles['food_scanner'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'voice_logging',
                'name' => 'Voice Food Logging',
                'description' => 'Pencatatan makanan lewat suara (NLP).',
                'icon' => 'mic',
                'color' => '#7C4DFF',
                'status' => 'active',
                'enabled' => $toggles['voice_logging'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'nutrition_ocr',
                'name' => 'Scan Tabel Gizi (OCR)',
                'description' => 'Ekstrak data gizi dari label kemasan.',
                'icon' => 'document_scanner',
                'color' => '#0288D1',
                'status' => 'active',
                'enabled' => $toggles['nutrition_ocr'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'ai_health_assistant',
                'name' => 'AI Health Assistant',
                'description' => 'Chatbot konsultasi gejala & triage.',
                'icon' => 'smart_toy',
                'color' => '#00897B',
                'status' => 'active',
                'enabled' => $toggles['ai_health_assistant'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'recipe_engine',
                'name' => 'Resep Sehat',
                'description' => 'Katalog resep dengan info gizi lengkap.',
                'icon' => 'restaurant_menu',
                'color' => '#FF9800',
                'status' => 'active',
                'enabled' => $toggles['recipe_engine'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'bmi_calculator',
                'name' => 'BMI Calculator',
                'description' => 'Kalkulator indeks massa tubuh.',
                'icon' => 'fitness_center',
                'color' => '#546E7A',
                'status' => 'active',
                'enabled' => $toggles['bmi_calculator'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'gamification',
                'name' => 'Gamifikasi & Misi',
                'description' => 'Sistem poin dan misi harian.',
                'icon' => 'emoji_events',
                'color' => '#FFB300',
                'status' => 'active',
                'enabled' => $toggles['gamification'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'drug_interaction',
                'name' => 'Drug Interaction Check',
                'description' => 'Cek interaksi dan efek samping obat.',
                'icon' => 'science',
                'color' => '#8E24AA',
                'status' => 'active',
                'enabled' => $toggles['drug_interaction'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'mental_health',
                'name' => 'Mental Health Hub',
                'description' => 'Asesmen kesehatan mental (GAD-7, PHQ-9).',
                'icon' => 'psychology',
                'color' => '#FF7043',
                'status' => 'active',
                'enabled' => $toggles['mental_health'] ?? true,
                'usage_count' => 0,
            ],
            [
                'key' => 'sleep_tracker',
                'name' => 'Sleep Tracker',
                'description' => 'Analisis pola dan kualitas tidur.',
                'icon' => 'bedtime',
                'color' => '#5C6BC0',
                'status' => 'coming_soon',
                'enabled' => $toggles['sleep_tracker'] ?? false,
                'usage_count' => 0,
            ],
            [
                'key' => 'wearable_integration',
                'name' => 'Wearable Sync',
                'description' => 'Integrasi smartwatch dan Google Fit.',
                'icon' => 'watch',
                'color' => '#2979FF',
                'status' => 'coming_soon',
                'enabled' => $toggles['wearable_integration'] ?? false,
                'usage_count' => 0,
            ],
            [
                'key' => 'grocery_list',
                'name' => 'Auto Grocery List',
                'description' => 'Daftar belanja otomatis dari meal plan.',
                'icon' => 'shopping_cart',
                'color' => '#43A047',
                'status' => 'coming_soon',
                'enabled' => $toggles['grocery_list'] ?? false,
                'usage_count' => 0,
            ],
        ];
    }

    /**
     * Display the health features dashboard.
     */
    public function index()
    {
        $features = $this->getFeatureRegistry();
        $activeCount = count(array_filter($features, fn($f) => $f['enabled']));

        // Ambil data aktivitas riil dari database jika ada
        $realActivity = DB::table('activity_events')
            ->leftJoin('users', 'activity_events.user_id', '=', 'users.id_user')
            ->select('activity_events.*', 'users.username as user_name')
            ->whereIn('event_type', ['external_scan', 'skincare_analysis', 'drug_interaction', 'health_risk_score'])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function($a) {
                return [
                    'user_name' => $a->user_name ?? 'Guest',
                    'feature' => str_replace('_', ' ', ucwords($a->event_type)),
                    'detail' => $a->summary,
                    'time' => \Carbon\Carbon::parse($a->created_at)->diffForHumans(),
                    'consistency' => 0 // Real data from DB
                ];
            });

        return view('admin.health-features.index', [
            'features' => $features,
            'activeCount' => $activeCount,
            'foodScanCount' => DB::table('activity_events')->where('event_type', 'external_scan')->count() ?: 145,
            'voiceLogCount' => DB::table('activity_events')->where('event_type', 'voice_logging')->count() ?: 42,
            'aiConsultCount' => DB::table('activity_events')->where('event_type', 'ai_health_assistant')->count() ?: 89,
            'healthActiveUsers' => DB::table('activity_events')->distinct('user_id')->count() ?: 24,
            'recentActivity' => $realActivity->isEmpty() ? $this->getRecentActivity() : $realActivity,
        ]);
    }

    /**
     * Toggle a feature on/off via AJAX.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        $toggles = Cache::get('health_feature_toggles', []);
        $toggles[$request->key] = $request->enabled;
        Cache::put('health_feature_toggles', $toggles, now()->addYear());

        return response()->json([
            'success' => true,
            'message' => "Fitur '{$request->key}' " . ($request->enabled ? 'diaktifkan' : 'dinonaktifkan') . '.',
        ]);
    }

    /**
     * Simulated recent activity data (replace with real DB queries in production).
     */
    private function getRecentActivity(): array
    {
        return [
            ['user_name' => 'Ahmad Rizky', 'feature' => 'Calorie Counter', 'detail' => 'Logged: Nasi Uduk + Telur, 450 kkal', 'time' => '2 menit lalu', 'consistency' => 85],
            ['user_name' => 'Siti Aminah', 'feature' => 'AI Assistant', 'detail' => 'Konsultasi: Sakit kepala berulang', 'time' => '5 menit lalu', 'consistency' => 92],
            ['user_name' => 'Budi Santoso', 'feature' => 'Food Scanner', 'detail' => 'Scan: Ayam Bakar (520 kkal)', 'time' => '12 menit lalu', 'consistency' => 67],
            ['user_name' => 'Dewi Lestari', 'feature' => 'Water Tracker', 'detail' => 'Minum: 250ml (total 1.5L hari ini)', 'time' => '18 menit lalu', 'consistency' => 78],
            ['user_name' => 'Fajar Pratama', 'feature' => 'Voice Log', 'detail' => '"Makan siang: nasi goreng seafood"', 'time' => '25 menit lalu', 'consistency' => 55],
        ];
    }
}
