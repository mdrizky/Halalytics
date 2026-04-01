<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use App\Services\ActivityEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ScanModel;
use App\Models\IntakeLog;
use Carbon\Carbon;

class AIAssistantController extends Controller
{
    protected $geminiService;
    protected $activityEventService;

    public function __construct(GeminiService $geminiService, ActivityEventService $activityEventService)
    {
        $this->geminiService = $geminiService;
        $this->activityEventService = $activityEventService;
    }

    /**
     * Analyze ingredients text using Gemini AI
     */
    public function analyzeIngredients(Request $request)
    {
        $request->validate([
            'ingredients_text' => 'required|string',
            'user_profile' => 'nullable|array',
            'product_name' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'user_watchlist' => 'nullable|array'
        ]);

        $text = $request->ingredients_text;
        $user = Auth::user();
        $familyId = $request->family_id;

        // Build health profile for AI context (either User or Family Member)
        $userContext = $this->resolveHealthContext($user, $familyId);

        // FITUR 3: String matching for Watchlist
        $watchlistAlert = [];
        if ($request->has('user_watchlist') && is_array($request->user_watchlist)) {
            $textLower = strtolower($text);
            foreach ($request->user_watchlist as $item) {
                if (\str_contains($textLower, strtolower($item))) {
                    $watchlistAlert[] = $item;
                    // Log to DB
                    \DB::table('watchlist_triggers')->insert([
                        'user_id' => $user->id_user,
                        'ingredient_name' => $item,
                        'product_name' => $request->product_name ?? 'Unknown',
                        'triggered_at' => \Carbon\Carbon::now(),
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ]);
                }
            }
        }

        try {
            $analysis = $this->geminiService->analyzeIngredients($text, $userContext);

            // Log intake if nutrition estimate is available
            if (isset($analysis['nutrition_estimate'])) {
                IntakeLog::create([
                    'user_id' => $user->id_user,
                    'product_id' => $request->product_id,
                    'product_name' => $request->product_name ?? 'Unknown Product',
                    'sugar_g' => $analysis['nutrition_estimate']['sugar_g'] ?? 0,
                    'sodium_mg' => $analysis['nutrition_estimate']['sodium_mg'] ?? 0,
                    'calories' => $analysis['nutrition_estimate']['calories'] ?? 0,
                    'logged_at' => Carbon::now()->toDateString()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'content' => $analysis,
                'watchlist_alert' => $watchlistAlert,
                'message' => 'Analysis completed and logged successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Analysis Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze ingredients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get daily intake statistics
     */
    public function getDailyIntake(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', Carbon::now()->toDateString());

        $logs = IntakeLog::where('user_id', $user->id_user)
            ->where('logged_at', $date)
            ->get();

        $totals = [
            'sugar_g' => $logs->sum('sugar_g'),
            'sodium_mg' => $logs->sum('sodium_mg'),
            'calories' => $logs->sum('calories'),
            'items_count' => $logs->count()
        ];

        // Theoretical limits (can be customized based on profile later)
        $limits = [
            'sugar_g' => 50,
            'sodium_mg' => 2300,
            'calories' => 2000
        ];

        return response()->json([
            'success' => true,
            'date' => $date,
            'totals' => $totals,
            'limits' => $limits
        ]);
    }

    /**
     * Personal Health Risk Score (daily aggregate sugar/sodium/fat).
     */
    public function getPersonalRiskScore(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', Carbon::now()->toDateString());

        $logs = IntakeLog::where('user_id', $user->id_user)
            ->where('logged_at', $date)
            ->get();

        $scanHistories = \App\Models\ScanHistory::where('user_id', $user->id_user)
            ->whereDate('created_at', $date)
            ->get(['nutrition_snapshot']);

        $fatFromSnapshots = $scanHistories->sum(function ($scan) {
            $snapshot = (array) ($scan->nutrition_snapshot ?? []);
            foreach (['fat_g', 'fat', 'total_fat', 'fat_100g'] as $key) {
                if (isset($snapshot[$key]) && is_numeric($snapshot[$key])) {
                    return (float) $snapshot[$key];
                }
            }
            return 0.0;
        });

        $totals = [
            'sugar_g' => round((float) $logs->sum('sugar_g'), 2),
            'sodium_mg' => round((float) $logs->sum('sodium_mg'), 2),
            'fat_g' => round((float) $fatFromSnapshots, 2),
            'calories' => (int) $logs->sum('calories'),
            'items_count' => (int) $logs->count(),
            'scan_items_count' => (int) $scanHistories->count(),
        ];

        $limits = [
            'sugar_g' => 50.0,
            'sodium_mg' => 2300.0,
            'fat_g' => 67.0,
            'calories' => 2000.0,
        ];

        $sugarPct = min(200, ($totals['sugar_g'] / $limits['sugar_g']) * 100);
        $sodiumPct = min(200, ($totals['sodium_mg'] / $limits['sodium_mg']) * 100);
        $fatPct = min(200, ($totals['fat_g'] / $limits['fat_g']) * 100);

        $riskScore = (int) round(($sugarPct * 0.4) + ($sodiumPct * 0.35) + ($fatPct * 0.25));
        $riskLevel = match (true) {
            $riskScore >= 120 => 'high',
            $riskScore >= 80 => 'moderate',
            default => 'low',
        };

        $alerts = [];
        if ($totals['sugar_g'] > $limits['sugar_g']) {
            $alerts[] = 'Asupan gula harian melewati batas rekomendasi.';
        }
        if ($totals['sodium_mg'] > $limits['sodium_mg']) {
            $alerts[] = 'Asupan sodium/garam harian melewati batas rekomendasi.';
        }
        if ($totals['fat_g'] > $limits['fat_g']) {
            $alerts[] = 'Asupan lemak harian melewati batas rekomendasi.';
        }
        if (empty($alerts)) {
            $alerts[] = 'Asupan hari ini masih dalam batas aman dasar.';
        }

        $recommendation = match ($riskLevel) {
            'high' => 'Kurangi konsumsi makanan tinggi gula/garam/lemak hari ini dan pertimbangkan konsultasi tenaga kesehatan bila berulang.',
            'moderate' => 'Jaga porsi makan berikutnya, pilih opsi rendah gula/garam/lemak untuk menurunkan risiko harian.',
            default => 'Pertahankan pola makan saat ini dan tetap pantau asupan harian.',
        };

        $this->activityEventService->logEvent(
            eventType: 'health_risk_score',
            userId: $user->id_user ?? null,
            username: $user->username ?? null,
            entityRef: $date,
            summary: "Personal risk score {$riskLevel} ({$riskScore})",
            status: 'success',
            payload: [
                'date' => $date,
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'sugar_g' => $totals['sugar_g'],
                'sodium_mg' => $totals['sodium_mg'],
                'fat_g' => $totals['fat_g'],
            ]
        );

        return response()->json([
            'success' => true,
            'date' => $date,
            'totals' => $totals,
            'limits' => $limits,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'alerts' => $alerts,
            'recommendation' => $recommendation,
            'disclaimer' => 'Skor ini untuk edukasi dan pemantauan mandiri, bukan diagnosis medis.'
        ]);
    }

    /**
     * Get personalized health advice for a specific scan
     */
    public function getPersonalHealthAdvice(Request $request)
    {
        $request->validate([
            'ingredients_text' => 'required|string',
            'health_profile' => 'required|array'
        ]);

        $text = $request->ingredients_text;
        $profile = $request->health_profile;

        $normalized = strtolower($text);
        $warnings = [];
        $riskScore = 0;

        $allergyText = strtolower((string) ($profile['allergy'] ?? $profile['allergies'] ?? ''));
        if ($allergyText !== '') {
            $allergens = collect(preg_split('/[,;|]/', $allergyText))
                ->map(fn ($i) => trim((string) $i))
                ->filter(fn ($i) => $i !== '')
                ->values();
            foreach ($allergens as $allergen) {
                if (str_contains($normalized, $allergen)) {
                    $warnings[] = "Terdeteksi alergen pribadi: {$allergen}.";
                    $riskScore += 45;
                }
            }
        }

        $medicalHistory = strtolower((string) ($profile['medical_history'] ?? ''));
        if ($medicalHistory !== '' && str_contains($medicalHistory, 'diabet')) {
            foreach (['glucose', 'sugar', 'gula', 'high fructose corn syrup', 'fructose'] as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    $warnings[] = "Bahan {$keyword} perlu dibatasi untuk profil diabetes.";
                    $riskScore += 20;
                    break;
                }
            }
        }

        foreach (['alcohol', 'ethanol', 'gelatin', 'lard'] as $sensitive) {
            if (str_contains($normalized, $sensitive)) {
                $warnings[] = "Bahan sensitif terdeteksi: {$sensitive}.";
                $riskScore += 15;
            }
        }

        $advice = null;
        try {
            $prompt = "Analyze ingredient safety briefly for this profile.\n"
                . "Ingredients: {$text}\n"
                . "Profile: " . json_encode($profile) . "\n"
                . "Return concise JSON with keys: recommendation,warnings.";
            $aiRaw = $this->geminiService->generateCustomContent($prompt);
            $decoded = is_string($aiRaw) ? json_decode($aiRaw, true) : $aiRaw;
            if (is_array($decoded)) {
                $advice = $decoded;
            }
        } catch (\Throwable $e) {
            Log::warning('Personal health advice AI fallback used: ' . $e->getMessage());
        }

        $riskLevel = match (true) {
            $riskScore >= 60 => 'high',
            $riskScore >= 30 => 'moderate',
            default => 'low',
        };
        $isSafe = $riskLevel !== 'high';
        $recommendation = $advice['recommendation'] ?? match ($riskLevel) {
            'high' => 'Tidak direkomendasikan untuk dikonsumsi tanpa konsultasi lebih lanjut.',
            'moderate' => 'Konsumsi terbatas dan pantau reaksi tubuh Anda.',
            default => 'Relatif aman berdasarkan data profil saat ini.',
        };
        $aiWarnings = is_array($advice['warnings'] ?? null) ? $advice['warnings'] : [];

        return response()->json([
            'success' => true,
            'data' => [
                'is_safe' => $isSafe,
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'warnings' => array_values(array_unique(array_merge($warnings, $aiWarnings))),
                'recommendation' => $recommendation,
                'disclaimer' => 'Hasil ini bersifat edukatif dan tidak menggantikan diagnosis medis.'
            ]
        ]);
    }
    /**
     * Generate a smart weekly report for the user
     */
    public function generateWeeklyReport(Request $request)
    {
        $user = Auth::user();
        $days = $request->get('days', 7);
        $startDate = Carbon::now()->subDays($days);

        try {
            // 1. Fetch statistics
            $scans = ScanModel::where('user_id', $user->id_user)
                ->where('tanggal_scan', '>=', $startDate)
                ->get();

            if ($scans->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No activity found in the last ' . $days . ' days',
                    'content' => null
                ]);
            }

            $stats = [
                'total_scans' => $scans->count(),
                'halal_count' => $scans->where('status_halal', 'halal')->count(),
                'haram_count' => $scans->where('status_halal', 'haram')->count(),
                'syubhat_count' => $scans->where('status_halal', 'syubhat')->count(),
                'healthy_count' => $scans->where('status_kesehatan', 'sehat')->count(),
                'unhealthy_count' => $scans->where('status_kesehatan', 'tidak_sehat')->count(),
                'top_categories' => $scans->groupBy('kategori')->map->count()->sortDesc()->take(3),
                'recent_products' => $scans->take(3)->pluck('nama_produk')->toArray()
            ];

            // 2. Build prompt for Gemini
            $prompt = "Provide a brief personal weekly health & halal summary based on these scan stats: " . json_encode($stats) . ". 
            The user profile is: " . json_encode([
                'allergy' => $user->allergy,
                'medical_history' => $user->medical_history,
                'goal' => $user->goal
            ]) . ". 
            Format as JSON: {'summary': 'text', 'tips': ['tip1', 'tip2'], 'highlight': 'text'}";

            $insight = null;
            try {
                $insight = $this->geminiService->generateCustomContent($prompt);
                
                // Decode if it's a string, or parse if array
                if (is_string($insight)) {
                    $insight = json_decode($insight, true);
                }
            } catch (\Exception $e) {
                Log::error('Gemini AI failed for Weekly Report: ' . $e->getMessage());
                $insight = [
                    'summary' => 'Analisis AI sedang tidak tersedia.',
                    'tips' => ['Lanjutkan kebiasaan baik Anda.'],
                    'highlight' => 'Tetap Sehat!',
                    'error' => $e->getMessage()
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Weekly report generated successfully',
                'stats' => $stats,
                'insight' => $insight
            ]);

        } catch (\Exception $e) {
            Log::error('Weekly Report General Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to resolve health context for either the main user or a family member
     */
    private function resolveHealthContext($user, $familyId = null)
    {
        if ($familyId) {
            $family = \App\Models\FamilyProfile::where('user_id', $user->id_user)->find($familyId);
            if ($family) {
                return [
                    'name' => $family->name,
                    'is_family_member' => true,
                    'age' => $family->age,
                    'gender' => $family->gender,
                    'medical_history' => $family->medical_history,
                    'allergies' => $family->allergies,
                    'diabetes' => str_contains(strtolower($family->medical_history ?? ''), 'diabetes'),
                    'goal' => 'Maintain health', // Default for family
                    'diet_preference' => null
                ];
            }
        }

        return [
            'name' => $user->full_name,
            'is_family_member' => false,
            'age' => $user->age,
            'gender' => $user->gender,
            'medical_history' => $user->medical_history,
            'allergies' => $user->allergy,
            'diabetes' => $user->has_diabetes,
            'goal' => $user->goal,
            'diet_preference' => $user->diet_preference
        ];
    }
}
