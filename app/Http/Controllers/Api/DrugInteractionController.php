<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\DrugInteraction;
use App\Models\AiQueryLog;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DrugInteractionController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Check interaction between two drugs
     */
    public function check(Request $request)
    {
        $request->validate([
            'drug_a_id' => 'nullable|exists:medicines,id_medicine',
            'drug_b_id' => 'nullable|exists:medicines,id_medicine',
            'drug_a_name' => 'nullable|string',
            'drug_b_name' => 'nullable|string'
        ]);

        $drugA = null;
        $drugB = null;

        // Resolve Drug A
        if ($request->drug_a_id) {
            $drugA = Medicine::where('id_medicine', $request->drug_a_id)->first();
        } elseif ($request->drug_a_name) {
            $drugA = Medicine::where('name', 'LIKE', "%{$request->drug_a_name}%")->first() ?: ['name' => $request->drug_a_name, 'generic_name' => $request->drug_a_name];
        }

        // Resolve Drug B
        if ($request->drug_b_id) {
            $drugB = Medicine::where('id_medicine', $request->drug_b_id)->first();
        } elseif ($request->drug_b_name) {
            $drugB = Medicine::where('name', 'LIKE', "%{$request->drug_b_name}%")->first() ?: ['name' => $request->drug_b_name, 'generic_name' => $request->drug_b_name];
        }

        if (!$drugA || !$drugB) {
            return response()->json([
                'success' => false,
                'message' => 'Kedua obat harus ditentukan.'
            ], 422);
        }

        // Check cache/DB for existing interaction if both are in DB
        if (is_object($drugA) && is_object($drugB)) {
            $existing = DrugInteraction::where(function($q) use ($drugA, $drugB) {
                $q->where('medicine_a_id', $drugA->id_medicine)->where('medicine_b_id', $drugB->id_medicine);
            })->orWhere(function($q) use ($drugA, $drugB) {
                $q->where('medicine_a_id', $drugB->id_medicine)->where('medicine_b_id', $drugA->id_medicine);
            })->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'source' => 'database',
                    'data' => $existing
                ]);
            }
        }

        // Use AI if not in DB
        try {
            $startTime = microtime(true);
            $aiResult = $this->geminiService->checkDrugInteraction(
                is_object($drugA) ? $drugA->toArray() : (array)$drugA,
                is_object($drugB) ? $drugB->toArray() : (array)$drugB
            );
            $endTime = microtime(true);

            // Log AI Query
            AiQueryLog::create([
                'id_user' => auth()->user()->id_user,
                'query_type' => 'interaction_check',
                'input_data' => ['drug_a' => $drugA, 'drug_b' => $drugB],
                'ai_response' => $aiResult,
                'processing_time' => ($endTime - $startTime) * 1000
            ]);

            // Save to DB for future if both were in DB
            if (is_object($drugA) && is_object($drugB) && isset($aiResult['has_interaction'])) {
                DrugInteraction::create([
                    'medicine_a_id' => $drugA->id_medicine,
                    'medicine_b_id' => $drugB->id_medicine,
                    'severity' => $aiResult['severity'] ?? 'moderate',
                    'description' => $aiResult['description'] ?? 'No interaction found',
                    'recommendation' => $aiResult['recommendation'] ?? null,
                    'ai_verified' => true,
                    'verified_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'source' => 'ai',
                'data' => $aiResult
            ]);

        } catch (\Exception $e) {
            Log::error('Drug interaction check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa interaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search drugs in master data
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        if (!$query) return response()->json(['success' => true, 'data' => []]);

        $results = Medicine::where('name', 'LIKE', "%{$query}%")
            ->orWhere('generic_name', 'LIKE', "%{$query}%")
            ->orWhere('brand_name', 'LIKE', "%{$query}%")
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
