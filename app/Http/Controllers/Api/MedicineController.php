<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineReminder;
use App\Models\HalalCriticalIngredient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;
use App\Services\ExternalApiService;

class MedicineController extends Controller
{
    private $geminiService;
    private $externalApiService;

    public function __construct(GeminiService $geminiService, ExternalApiService $externalApiService)
    {
        $this->geminiService = $geminiService;
        $this->externalApiService = $externalApiService;
    }

    // AI Symptom-to-Medicine Mapping (Enhanced Phase 4)
    public function analyzeSymptoms(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'user_id' => 'nullable|exists:users,id_user', // Allow flexible user ID for API testing
            'family_id' => 'nullable|integer'
        ]);

        $symptoms = $request->input('symptoms');
        $userId = auth()->user() ? auth()->user()->id_user : $request->input('user_id');
        $familyId = $request->input('family_id');

        try {
            // Step 1: Resolve health context for AI
            $user = \App\Models\User::find($userId);
            $userContext = $this->resolveHealthContext($user, $familyId);

            // Step 2: Use centralized GeminiService for AI Analysis with context
            $aiResult = $this->geminiService->analyzeSymptoms($symptoms, $userContext);

            // Step 2: Find Medicines with Active Ingredients
            $medicines = $this->findMedicinesByIngredients($aiResult['recommended_ingredients'] ?? []);

            // Step 3: Halal Filter (Database level)
            $halalMedicines = $medicines->filter(function($medicine) {
                return $medicine->halal_status === 'halal';
            });

            return response()->json([
                'success' => true,
                'symptoms_analysis' => [
                    'condition' => $aiResult['condition'] ?? 'Unknown',
                    'gejala_terkait' => $aiResult['gejala_terkait'] ?? [],
                    'active_ingredients' => $aiResult['recommended_ingredients'] ?? [],
                    'severity' => $aiResult['severity'] ?? 'mild',
                    'emergency_warning' => $aiResult['emergency_warning'] ?? null,
                    'halal_check' => $aiResult['halal_check'] ?? ['status' => 'unknown', 'notes' => ''],
                    'usage_instructions' => $aiResult['usage_instructions'] ?? '',
                    'lifestyle_advice' => $aiResult['lifestyle_advice'] ?? '',
                    'dosage_guidelines' => $aiResult['dosage_guidelines'] ?? '',
                    'recommended_medicines_list' => $aiResult['recommended_medicines_list'] ?? [],
                    'recommendation' => $aiResult['recommendation'] ?? ''
                ],
                'recommended_medicines' => $halalMedicines->values(),
                'all_medicines' => $medicines->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Symptom analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze symptoms: ' . $e->getMessage()
            ], 500);
        }
    }

    // Find medicines by active ingredients
    private function findMedicinesByIngredients(array $ingredients)
    {
        $medicines = collect();

        foreach ($ingredients as $ingredient) {
            $matches = Medicine::active()
                ->where(function($query) use ($ingredient) {
                    $query->where('name', 'LIKE', "%{$ingredient}%")
                          ->orWhere('generic_name', 'LIKE', "%{$ingredient}%")
                          ->orWhere('ingredients', 'LIKE', "%{$ingredient}%");
                })
                ->get();

            $medicines = $medicines->merge($matches);
        }

        return $medicines->unique('id_medicine');
    }

    // Hybrid Medicine Search (Local + International APIs)
    public function searchMedicine(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'search_type' => 'in:name,barcode,generic_name'
        ]);

        $query = $request->input('query');
        $searchType = $request->input('search_type', 'name');

        // Step 1: Search Local Database
        $localResults = $this->searchLocalDatabase($query, $searchType);

        // Step 2: Try International APIs
        $internationalResults = $this->searchInternationalAPIs($query);

        // Merge Results
        $mergedResults = $localResults->merge($internationalResults);

        return response()->json([
            'success' => true,
            'source' => 'hybrid',
            'data' => $mergedResults->values()
        ]);
    }

    private function searchLocalDatabase($query, $searchType)
    {
        $searchFields = explode(',', $searchType);
        
        return Medicine::active()->where(function($q) use ($query, $searchFields) {
            foreach ($searchFields as $field) {
                $field = trim($field);
                if ($field === 'name') {
                    $q->orWhere('name', 'LIKE', "%{$query}%");
                } elseif ($field === 'barcode') {
                    $q->orWhere('barcode', $query);
                } elseif ($field === 'generic_name') {
                    $q->orWhere('generic_name', 'LIKE', "%{$query}%");
                }
            }
        })->get()->map(function($m) {
            $m->id_medicine = $m->id_medicine; // Ensure id_medicine is present
            $m->source = 'local';
            return $m;
        });
    }

    private function searchInternationalAPIs($query)
    {
        $results = collect();

        // OpenFDA is the primary external source for medicine labels and dosage guidance.
        try {
            $fdaResult = $this->externalApiService->searchOpenFDA($query);
            if ($fdaResult['found'] ?? false) {
                $savedMedicine = $this->externalApiService->upsertMedicineFromOpenFDA($fdaResult, $query);
                if ($savedMedicine) {
                    $savedMedicine->source = 'openfda';
                    $results->push($savedMedicine);
                }
            }
        } catch (\Exception $e) {
            Log::error('OpenFDA API failed: ' . $e->getMessage());
        }

        // Fallback via AI extracted ingredients, then search each ingredient in OpenFDA.
        if ($results->isEmpty() && !is_numeric($query)) {
            try {
                $ingredientsResult = $this->geminiService->generateText("Ekstrak daftar bahan aktif utama dari nama obat/keluhan ini: '{$query}'. Balas HANYA dengan list JSON: [\"bahan1\", \"bahan2\"]");
                if (is_array($ingredientsResult)) {
                    foreach ($ingredientsResult as $ingredient) {
                        $fdaResult = $this->externalApiService->searchOpenFDA((string)$ingredient);
                        if ($fdaResult['found'] ?? false) {
                            $savedMedicine = $this->externalApiService->upsertMedicineFromOpenFDA($fdaResult, (string)$ingredient);
                            if ($savedMedicine) {
                                $savedMedicine->source = 'openfda';
                                $results->push($savedMedicine);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('International Fallback failed: ' . $e->getMessage());
            }
        }

        return $results->unique(function ($medicine) {
            return strtolower(($medicine->name ?? '') . '|' . ($medicine->generic_name ?? ''));
        })->values();
    }

    // Check halal status against critical ingredients
    private function checkHalalStatus($medicineName)
    {
        $criticalIngredients = HalalCriticalIngredient::active()->get();
        
        $medicineName = strtolower($medicineName);
        
        foreach ($criticalIngredients as $ingredient) {
            if (strpos($medicineName, strtolower($ingredient->name)) !== false) {
                return $ingredient->status;
            }
        }

        return 'syubhat'; // Default to syubhat for unknown
    }

    /**
     * Generate safe medication schedule preview with mandatory medical disclaimer.
     */
    public function generateSafeSchedule(Request $request)
    {
        $request->validate([
            'medicine_id' => 'nullable|exists:medicines,id_medicine',
            'medicine_name' => 'nullable|string|max:255|required_without:medicine_id',
            'frequency_per_day' => 'nullable|integer|min:1|max:6',
            'dosage' => 'nullable|string|max:120',
            'wake_time' => 'nullable|date_format:H:i',
            'sleep_time' => 'nullable|date_format:H:i',
            'meal_relation' => 'nullable|in:before_meal,after_meal,with_meal,any',
            'start_date' => 'nullable|date',
            'duration_days' => 'nullable|integer|min:1|max:30',
        ]);

        $medicine = null;
        if ($request->filled('medicine_id')) {
            $medicine = Medicine::active()->where('id_medicine', $request->medicine_id)->first();
        } elseif ($request->filled('medicine_name')) {
            $medicine = Medicine::active()
                ->where('name', 'like', '%' . $request->medicine_name . '%')
                ->orWhere('generic_name', 'like', '%' . $request->medicine_name . '%')
                ->first();
        }

        $frequency = (int) ($request->input('frequency_per_day')
            ?? ($medicine?->frequency_per_day ? (int)$medicine->frequency_per_day : 1));
        $frequency = max(1, min(6, $frequency));

        $wakeTime = $request->input('wake_time', '06:00');
        $sleepTime = $request->input('sleep_time', '22:00');
        $mealRelation = $request->input('meal_relation', 'any');
        $startDate = Carbon::parse($request->input('start_date', now()->toDateString()));
        $durationDays = (int) $request->input('duration_days', 7);

        $scheduleTimes = $this->buildScheduleTimes($frequency, $wakeTime, $sleepTime, $mealRelation);
        $dosage = $request->input('dosage') ?: ($medicine?->dosage_info ?? 'Ikuti etiket obat');

        $mealInstruction = match ($mealRelation) {
            'before_meal' => 'Minum 30 menit sebelum makan.',
            'after_meal' => 'Minum 10-30 menit sesudah makan.',
            'with_meal' => 'Minum bersamaan dengan makan.',
            default => 'Ikuti petunjuk etiket atau resep dokter terkait waktu makan.',
        };

        return response()->json([
            'success' => true,
            'message' => 'Jadwal minum obat berhasil dibuat (preview).',
            'data' => [
                'medicine' => [
                    'id_medicine' => $medicine?->id_medicine,
                    'name' => $medicine?->name ?? $request->input('medicine_name', 'Obat'),
                    'generic_name' => $medicine?->generic_name,
                    'source' => $medicine?->source ?? 'manual_input',
                ],
                'dosage' => $dosage,
                'frequency_per_day' => $frequency,
                'meal_relation' => $mealRelation,
                'meal_instruction' => $mealInstruction,
                'schedule_times' => $scheduleTimes,
                'start_date' => $startDate->toDateString(),
                'end_date' => $startDate->copy()->addDays($durationDays - 1)->toDateString(),
                'duration_days' => $durationDays,
                'disclaimer' => 'Jadwal ini hanya referensi edukasi. Ikuti etiket kemasan dan resep dokter/apoteker. Hentikan pemakaian dan cari bantuan medis jika muncul efek samping berat.',
            ]
        ]);
    }

    private function buildScheduleTimes(int $frequency, string $wakeTime, string $sleepTime, string $mealRelation): array
    {
        if (in_array($mealRelation, ['before_meal', 'after_meal', 'with_meal'], true) && $frequency <= 3) {
            $baseMealSlots = ['08:00', '13:00', '19:00'];
            $offsetMinutes = match ($mealRelation) {
                'before_meal' => -30,
                'after_meal' => 30,
                default => 0,
            };

            return collect(array_slice($baseMealSlots, 0, $frequency))
                ->map(function ($slot) use ($offsetMinutes) {
                    return Carbon::createFromFormat('H:i', $slot)
                        ->addMinutes($offsetMinutes)
                        ->format('H:i');
                })
                ->values()
                ->all();
        }

        $wake = Carbon::createFromFormat('H:i', $wakeTime);
        $sleep = Carbon::createFromFormat('H:i', $sleepTime);
        if ($sleep->lessThanOrEqualTo($wake)) {
            $sleep->addDay();
        }

        if ($frequency === 1) {
            return [$wake->copy()->addHours(2)->format('H:i')];
        }

        $windowMinutes = $wake->diffInMinutes($sleep);
        $interval = (int) floor($windowMinutes / ($frequency - 1));

        $times = [];
        for ($i = 0; $i < $frequency; $i++) {
            $times[] = $wake->copy()->addMinutes($interval * $i)->format('H:i');
        }

        return $times;
    }

    // Create Medicine Reminder
    public function createReminder(Request $request)
    {
        $request->validate([
            'id_medicine' => 'required|exists:medicines,id_medicine',
            'symptoms' => 'nullable|string',
            'frequency_per_day' => 'required|integer|min:1|max:6',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
            'family_id' => 'nullable|integer'
        ]);

        $userId = auth()->user() ? auth()->user()->id_user : $request->input('id_user');
        $familyId = $request->input('family_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID required'], 422);
        }

        $medicine = Medicine::findOrFail($request->id_medicine);
        
        // Calculate schedule times based on frequency
        $scheduleTimes = $this->calculateScheduleTimes($request->frequency_per_day);

        $reminder = MedicineReminder::create([
            'id_user' => $userId,
            'id_medicine' => $request->id_medicine,
            'symptoms' => $request->symptoms,
            'schedule_times' => $scheduleTimes,
            'frequency_per_day' => $request->frequency_per_day,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
            'notes' => $request->notes,
            'family_id' => $familyId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Medicine reminder created successfully',
            'reminder' => $reminder->load('medicine')
        ]);
    }

    // Calculate schedule times based on frequency
    private function calculateScheduleTimes($frequency)
    {
        $times = [];
        
        switch ($frequency) {
            case 1:
                $times = ['08:00'];
                break;
            case 2:
                $times = ['08:00', '20:00'];
                break;
            case 3:
                $times = ['08:00', '14:00', '20:00'];
                break;
            case 4:
                $times = ['08:00', '12:00', '16:00', '20:00'];
                break;
            case 6:
                $times = ['06:00', '10:00', '14:00', '18:00', '22:00', '02:00'];
                break;
            default:
                $times = ['08:00'];
        }

        return $times;
    }

    // Get user reminders
    public function getUserReminders($userId = null)
    {
        $userId = auth()->user() ? auth()->user()->id_user : $userId;
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID required'], 422);
        }
        $reminders = MedicineReminder::with('medicine')
            ->forUser($userId)
            ->active()
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reminders
        ]);
    }

    // Mark medicine as taken
    public function markAsTaken(Request $request)
    {
        $request->validate([
            'id_reminder' => 'required|exists:medicine_reminders,id_reminder',
            'user_id' => 'required|exists:users,id_user'
        ]);

        $reminder = MedicineReminder::findOrFail($request->id_reminder);
        $userId = auth()->user() ? auth()->user()->id_user : $request->user_id;
        
        // Verify ownership
        if ($reminder->id_user != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $reminder->markAsTaken();

        return response()->json([
            'success' => true,
            'message' => 'Medicine marked as taken',
            'taken_times' => $reminder->taken_times
        ]);
    }

    // Get next dose for user
    public function getNextDose($userId = null)
    {
        $userId = auth()->user() ? auth()->user()->id_user : $userId;

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID required'], 422);
        }
        $reminders = MedicineReminder::with('medicine')
            ->forUser($userId)
            ->active()
            ->get();

        $nextDoses = [];

        foreach ($reminders as $reminder) {
            $nextDose = $reminder->getNextDoseTime();
            if ($nextDose) {
                $nextDoses[] = [
                    'reminder_id' => $reminder->id_reminder,
                    'medicine_name' => $reminder->medicine->name,
                    'next_dose_time' => $nextDose->toISOString(),
                    'dose_info' => $reminder->medicine->dosage_info
                ];
            }
        }

        // Sort by next dose time
        usort($nextDoses, function($a, $b) {
            return strtotime($a['next_dose_time']) - strtotime($b['next_dose_time']);
        });

        return response()->json([
            'success' => true,
            'next_doses' => $nextDoses
        ]);
    }

    // Get single medicine detail
    public function show($id)
    {
        try {
            $medicine = Medicine::active()->findOrFail($id);
            
            // Set source explicitly for single detail as well
            $medicine->source = 'local';
            // Android uses MedicineSearchResponse (List of MedicineData) for this endpoint
            return response()->json([
                'success' => true,
                'data' => [$medicine]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Obat tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Helper to resolve health context for either the main user or a family member
     */
    private function resolveHealthContext($user, $familyId = null)
    {
        if ($familyId && $user) {
            $family = \App\Models\FamilyProfile::where('user_id', $user->id_user)->find($familyId);
            if ($family) {
                return [
                    'name' => $family->name,
                    'is_family_member' => true,
                    'age' => $family->age,
                    'gender' => $family->gender,
                    'medical_history' => $family->medical_history,
                    'allergies' => $family->allergies,
                ];
            }
        }

        if ($user) {
            return [
                'name' => $user->full_name,
                'is_family_member' => false,
                'age' => $user->age,
                'gender' => $user->gender,
                'medical_history' => $user->medical_history,
                'allergies' => $user->allergy,
                'dietary_preference' => $user->diet_preference,
            ];
        }

        return [];
    }
}
