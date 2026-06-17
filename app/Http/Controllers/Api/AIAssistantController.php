<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AiResponseFormatter;
use App\Services\AI\FoodAnalysisOrchestrator;
use App\Services\AI\PromptBuilderService;
use App\Services\GeminiService;
use App\Services\ActivityEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ScanModel;
use App\Models\IntakeLog;
use App\Models\MedicalProfile;
use Carbon\Carbon;

class AIAssistantController extends Controller
{
    protected $geminiService;
    protected $activityEventService;
    protected $aiResponseFormatter;

    public function __construct(
        GeminiService $geminiService,
        ActivityEventService $activityEventService,
        AiResponseFormatter $aiResponseFormatter,
        protected FoodAnalysisOrchestrator $foodAnalysisOrchestrator
    ) {
        $this->geminiService = $geminiService;
        $this->activityEventService = $activityEventService;
        $this->aiResponseFormatter = $aiResponseFormatter;
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

        $medicalProfile = MedicalProfile::where('id_user', $user->id_user)->first();

        $userContext = [
            'name' => $user->full_name,
            'age' => $user->age,
            'gender' => $user->gender ?? null,
            'medical_history' => $user->medical_history,
            'allergies' => $user->allergy,
            'goal' => $user->goal ?? null,
            'diet_preference' => $user->diet_preference ?? null,
            'bmi' => $user->bmi ?? null,
            'activity_level' => $user->activity_level ?? null,
            'thresholds' => $medicalProfile ? $medicalProfile->thresholds : null,
        ];

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
            $analysis = $this->foodAnalysisOrchestrator->analyzeIngredients($text, array_merge($userContext, [
                'user_id' => $user->id_user,
            ]), [
                'product_name' => $request->product_name,
                'barcode' => $request->barcode ?? null,
            ]);

            // Log intake to daily_intakes table (Dashboard Sync)
            if (isset($analysis['nutrition_estimate'])) {
                $today = now()->format('Y-m-d');
                $nutrition = $analysis['nutrition_estimate'];
                
                \App\Models\DailyIntake::updateOrCreate(
                    ['user_id' => $user->id_user, 'intake_date' => $today],
                    [
                        'total_sugar_g' => \DB::raw('total_sugar_g + ' . ($nutrition['sugar_g'] ?? 0)),
                        'total_sodium_mg' => \DB::raw('total_sodium_mg + ' . ($nutrition['sodium_mg'] ?? 0)),
                        'total_calories' => \DB::raw('total_calories + ' . ($nutrition['calories'] ?? 0)),
                        'total_carbs_g' => \DB::raw('total_carbs_g + ' . ($nutrition['carbs_g'] ?? 0)),
                        'total_protein_g' => \DB::raw('total_protein_g + ' . ($nutrition['protein_g'] ?? 0)),
                        'total_fat_g' => \DB::raw('total_fat_g + ' . ($nutrition['fat_g'] ?? 0)),
                    ]
                );

                // Also keep legacy intake_logs for history
                IntakeLog::create([
                    'user_id' => $user->id_user,
                    'product_id' => $request->product_id,
                    'product_name' => $request->product_name ?? 'Unknown Product',
                    'sugar_g' => $nutrition['sugar_g'] ?? 0,
                    'sodium_mg' => $nutrition['sodium_mg'] ?? 0,
                    'calories' => $nutrition['calories'] ?? 0,
                    'logged_at' => $today
                ]);
            }
            
            $formatted = $this->aiResponseFormatter->forMobile($analysis);

            return response()->json([
                'success' => true,
                'content' => $formatted,
                'watchlist_alert' => $watchlistAlert,
                'message' => 'Analysis completed and logged successfully',
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

        $medicalProfile = MedicalProfile::where('id_user', $user->id_user)->first();
        $limits = $medicalProfile ? $medicalProfile->thresholds : [
            'calories' => 2000,
            'sugar_g' => 50,
            'sodium_mg' => 2300,
            'fat_g' => 67,
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

        $medicalProfile = MedicalProfile::where('id_user', $user->id_user)->first();
        $limits = $medicalProfile ? $medicalProfile->thresholds : [
            'calories' => 2000.0,
            'sugar_g' => 50.0,
            'sodium_mg' => 2300.0,
            'fat_g' => 67.0,
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

            $intakes = \App\Models\DailyIntake::where('user_id', $user->id_user)
                ->where('intake_date', '>=', $startDate->toDateString())
                ->get();

            $medicalProfile = MedicalProfile::where('id_user', $user->id_user)->first();
            $thresholds = $medicalProfile ? $medicalProfile->thresholds : [
                'calories' => 2000,
                'sugar_g' => 50,
                'sodium_mg' => 2300,
                'fat_g' => 67,
            ];

            if ($scans->isEmpty() && $intakes->isEmpty()) {
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
                'avg_daily_calories' => round($intakes->avg('total_calories'), 1),
                'avg_daily_sugar' => round($intakes->avg('total_sugar_g'), 1),
                'avg_daily_sodium' => round($intakes->avg('total_sodium_mg'), 1),
                'top_categories' => $scans->groupBy('kategori')->map->count()->sortDesc()->take(3),
            ];

            // 2. Build prompt for Gemini
            $prompt = "Provide a professional personal weekly health & halal summary based on these stats: " . json_encode($stats) . ". 
            The user profile is: " . json_encode([
                'medical_history' => $user->medical_history,
                'goal' => $user->goal,
                'thresholds' => $thresholds
            ]) . ". 
            Format as JSON: {'summary': 'text in Indonesian', 'tips': ['tip1', 'tip2'], 'highlight': 'text', 'score': 0-100}";

            $insight = null;
            try {
                $insight = $this->geminiService->generateCustomContent($prompt);
                if (is_string($insight)) {
                    $insight = json_decode($insight, true);
                }
            } catch (\Exception $e) {
                Log::error('Gemini AI failed for Weekly Report: ' . $e->getMessage());
                $insight = [
                    'summary' => 'Analisis AI sedang tidak tersedia.',
                    'tips' => ['Lanjutkan kebiasaan baik Anda.'],
                    'highlight' => 'Tetap Sehat!',
                    'score' => 70
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Weekly report generated successfully',
                'stats' => $stats,
                'insight' => $insight,
                'thresholds' => $thresholds
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
     * Mobile AI chat — personalized, no placeholder responses.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        $user = Auth::user();
        $message = trim($request->input('message'));

        // Handle anonymous users (promo page visitors)
        if (!$user) {
            $userContext = [
                'user_name' => 'Pengunjung',
                'user_age' => '-',
                'user_gender' => '-',
                'user_weight' => '-',
                'user_height' => '-',
                'user_bmi' => '-',
                'user_diseases' => '-',
                'user_allergies' => '-',
                'user_diet_preference' => '-',
                'user_activity_level' => '-',
                'user_blood_type' => '-',
                'user_message' => $message,
                'is_guest' => true,
                'recent_scans' => 'Belum ada data scan.',
                'recent_intakes' => 'Belum ada log nutrisi.',
            ];
        } else {
            $userContext = [
                'user_name' => $user->full_name ?? $user->username ?? 'Pengguna',
                'user_age' => $user->age ?? '-',
                'user_gender' => $user->gender ?? '-',
                'user_weight' => $user->weight ?? '-',
                'user_height' => $user->height ?? '-',
                'user_bmi' => $user->bmi ?? '-',
                'user_diseases' => $user->medical_history ?? '-',
                'user_allergies' => $user->allergy ?? '-',
                'user_diet_preference' => $user->diet_preference ?? '-',
                'user_activity_level' => $user->activity_level ?? '-',
                'user_blood_type' => $user->blood_type ?? '-',
                'user_message' => $message,
                'is_guest' => false,
            ];

            // Fetch Recent Scans & Intake History for AI Context
            try {
                $recentScans = \App\Models\ScanHistory::where('user_id', $user->id_user)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['product_name', 'halal_status', 'barcode', 'nutrition_snapshot'])
                    ->toArray();

                $scanDetail = [];
                foreach ($recentScans as $scan) {
                    $detail = $scan['product_name'] ?? 'Unknown';
                    if (!empty($scan['halal_status'])) {
                        $detail .= ' (' . $scan['halal_status'] . ')';
                    }
                    if (!empty($scan['nutrition_snapshot'])) {
                        $nutri = is_string($scan['nutrition_snapshot'])
                            ? $scan['nutrition_snapshot']
                            : json_encode($scan['nutrition_snapshot']);
                        $detail .= ' - Gizi: ' . $nutri;
                    }
                    $scanDetail[] = $detail;
                }

                $userContext['recent_scans'] = !empty($scanDetail) ? implode("; ", $scanDetail) : 'Belum ada data scan.';

                $recentIntakes = IntakeLog::where('user_id', $user->id_user)
                    ->orderBy('logged_at', 'desc')
                    ->limit(5)
                    ->get(['product_name', 'sugar_g', 'sodium_mg', 'calories'])
                    ->toArray();

                $userContext['recent_intakes'] = !empty($recentIntakes) ? json_encode($recentIntakes) : 'Belum ada log nutrisi.';
            } catch (\Exception $e) {
                Log::warning("Failed to fetch AI memory context: " . $e->getMessage());
            }
        }

        try {
            if (!$user) {
                $userContext['user_id'] = 'guest_' . uniqid();
            } else {
                $userContext['user_id'] = $user->id_user;
            }
            
            $reply = $this->foodAnalysisOrchestrator->chat($message, $userContext);

            if ($reply === '') {
                $reply = 'Maaf, respons AI kosong. Silakan coba ulang dengan pertanyaan yang lebih spesifik.';
            }

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('AI chat error: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'reply' => 'Maaf, AI Halalytics sedang sibuk. Silakan coba lagi sebentar. Untuk pertanyaan mendesak terkait kesehatan, hubungi tenaga medis terdekat.',
            ]);
        }
    }

}
