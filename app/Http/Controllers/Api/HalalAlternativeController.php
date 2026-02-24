<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\HalalAlternative;
use App\Models\AiQueryLog;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HalalAlternativeController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Get halal alternatives for a drug/medicine
     */
    public function getAlternatives(Request $request)
    {
        $request->validate([
            'drug_id' => 'required|exists:medicines,id_medicine'
        ]);

        try {
            $medicine = Medicine::where('id_medicine', $request->drug_id)->firstOrFail();

            // Check if medicine is already halal
            if ($medicine->halal_status === 'halal') {
                return response()->json([
                    'success' => true,
                    'message' => 'Obat ini sudah berstatus Halal.',
                    'is_already_halal' => true,
                    'data' => $medicine
                ]);
            }

            // Check DB for existing alternatives
            $existing = HalalAlternative::with('alternativeMedicine')
                ->where('original_medicine_id', $medicine->id_medicine)
                ->get();

            if ($existing->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'source' => 'database',
                    'data' => $existing
                ]);
            }

            // Use AI to find alternatives
            $startTime = microtime(true);
            $aiResult = $this->geminiService->findHalalAlternative($medicine->toArray());
            $endTime = microtime(true);

            // Log AI Query
            AiQueryLog::create([
                'id_user' => auth()->user()->id_user,
                'query_type' => 'halal_alternative',
                'input_data' => ['drug' => $medicine->name],
                'ai_response' => $aiResult,
                'processing_time' => ($endTime - $startTime) * 1000
            ]);

            // Map AI results to DB if possible
            return response()->json([
                'success' => true,
                'source' => 'ai',
                'data' => $aiResult
            ]);

        } catch (\Exception $e) {
            Log::error('Halal alternative check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari alternatif halal: ' . $e->getMessage()
            ], 500);
        }
    }
}
