<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ScanModel;
use App\Models\IntakeLog;
use Carbon\Carbon;

class AIAssistantController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
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

        // Custom prompt for health advice
        $prompt = "As a health assistant, analyze these ingredients: '{$text}'. 
        The user has the following medical profile: " . json_encode($profile) . ". 
        Provide specific advice on whether this product is safe for them. 
        Format as JSON: {'is_safe': boolean, 'warnings': [], 'recommendation': 'text'}";

        // We can reuse the analyzeIngredients logic with a custom prompt if we modify GeminiService
        // For now, let's keep it simple.

        return response()->json([
            'success' => true,
            'advice' => 'Feature coming soon with full Gemini integration'
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
