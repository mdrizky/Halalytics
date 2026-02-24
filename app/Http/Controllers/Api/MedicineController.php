<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineReminder;
use App\Models\HalalCriticalIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;

class MedicineController extends Controller
{
    private $geminiService;
    private $rxNavUrl = 'https://rxnav.nlm.nih.gov/REST/drugs.json';
    private $openFoodFactsUrl = 'https://world.openfoodfacts.org/api/v0/product';

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
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

        // Step 1: Try RxNav API directly
        try {
            $rxResponse = Http::timeout(5)->get($this->rxNavUrl, ['name' => $query]);
            if ($rxResponse->successful()) {
                $rxData = $rxResponse->json();
                $rxResults = $this->parseRxNavData($rxData);
                if ($rxResults->isNotEmpty()) {
                    $results = $results->merge($rxResults);
                }
            }
        } catch (\Exception $e) {
            Log::error('RxNav API failed: ' . $e->getMessage());
        }

        // Step 2: Fallback - if no results, use AI to find ingredients then search RxNav
        if ($results->isEmpty() && !is_numeric($query)) {
            try {
                $ingredientsResult = $this->geminiService->generateText("Ekstrak daftar bahan aktif utama dari nama obat/keluhan ini: '{$query}'. Balas HANYA dengan list JSON: [\"bahan1\", \"bahan2\"]");
                if (is_array($ingredientsResult)) {
                    foreach ($ingredientsResult as $ingredient) {
                        $rxResponse = Http::timeout(5)->get($this->rxNavUrl, ['name' => $ingredient]);
                        if ($rxResponse->successful()) {
                            $results = $results->merge($this->parseRxNavData($rxResponse->json()));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('International Fallback failed: ' . $e->getMessage());
            }
        }

        // Step 3: Try OpenFoodFacts (for barcode)
        if (is_numeric($query)) {
            try {
                $offResponse = Http::timeout(5)->get("{$this->openFoodFactsUrl}/{$query}.json");
                if ($offResponse->successful()) {
                    $offData = $offResponse->json();
                    $results = $results->merge($this->parseOpenFoodFactsData($offData));
                }
            } catch (\Exception $e) {
                Log::error('OpenFoodFacts API failed: ' . $e->getMessage());
            }
        }

        return $results->unique('name');
    }

    private function parseRxNavData($data)
    {
        $medicines = collect();

        if (isset($data['drugGroup']['conceptGroup'])) {
            foreach ($data['drugGroup']['conceptGroup'] as $group) {
                if (isset($group['conceptProperties'])) {
                    foreach ($group['conceptProperties'] as $concept) {
                        $medicine = $this->createMedicineFromRxNav($concept);
                        if ($medicine) {
                            $medicines->push($medicine);
                        }
                    }
                }
            }
        }

        return $medicines;
    }

    private function createMedicineFromRxNav($concept)
    {
        try {
            // Check halal critical ingredients
            $halalStatus = $this->checkHalalStatus($concept['name'] ?? '');

            return [
                'id_medicine' => crc32($concept['name'] ?? 'unknown_rxnav'), // Synthetic ID
                'name' => $concept['name'] ?? 'Unknown',
                'generic_name' => $concept['synonym'] ?? '',
                'source' => 'rxnav',
                'halal_status' => $halalStatus,
                'dosage_form' => $concept['doseForm'] ?? null,
                'description' => 'Imported from RxNav database',
                'active' => true
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseOpenFoodFactsData($data)
    {
        if (!isset($data['product'])) {
            return collect();
        }

        $product = $data['product'];
        $halalStatus = $this->checkHalalStatus($product['product_name'] ?? '');

        return collect([[
            'id_medicine' => crc32($product['product_name'] ?? 'unknown_off'), // Synthetic ID
            'name' => $product['product_name'] ?? 'Unknown',
            'generic_name' => '',
            'barcode' => $product['code'] ?? null,
            'source' => 'openfoodfacts',
            'halal_status' => $halalStatus,
            'ingredients' => $product['ingredients_text'] ?? null,
            'description' => 'Imported from Open Food Facts',
            'active' => true
        ]]);
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
