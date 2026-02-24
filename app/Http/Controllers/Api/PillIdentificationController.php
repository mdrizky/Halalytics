<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\PillIdentification;
use App\Models\AiQueryLog;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PillIdentificationController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Identify a pill from image
     */
    public function identify(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
            'shape' => 'nullable|string',
            'color' => 'nullable|string'
        ]);

        try {
            // Save image
            $path = $request->file('image')->store('pills', 'public');
            $imageUrl = url(Storage::url($path));
            $imageBase64 = base64_encode(file_get_contents($request->file('image')->path()));

            $startTime = microtime(true);
            $aiResult = $this->geminiService->identifyPill(
                $imageBase64,
                $request->input('shape'),
                $request->input('color')
            );
            $endTime = microtime(true);

            // Log AI Query
            AiQueryLog::create([
                'id_user' => auth()->user()->id_user,
                'query_type' => 'pill_identify',
                'input_data' => ['image_path' => $path, 'shape' => $request->shape, 'color' => $request->color],
                'ai_response' => $aiResult,
                'processing_time' => ($endTime - $startTime) * 1000
            ]);

            // Save identification result if confidence is high
            $medicineId = null;
            if (isset($aiResult['possible_drugs'][0]) && $aiResult['possible_drugs'][0]['confidence'] > 0.7) {
                $possibleDrug = $aiResult['possible_drugs'][0];
                $medicine = Medicine::where('name', 'LIKE', "%{$possibleDrug['name']}%")->first();
                if ($medicine) {
                    $medicineId = $medicine->id_medicine;
                }
            }

            PillIdentification::create([
                'id_medicine' => $medicineId,
                'shape' => $aiResult['visual_features']['shape'] ?? $request->shape,
                'color' => $aiResult['visual_features']['color'] ?? $request->color,
                'imprint' => $aiResult['visual_features']['imprint'] ?? null,
                'image_url' => $imageUrl
            ]);

            return response()->json([
                'success' => true,
                'data' => $aiResult,
                'image_url' => $imageUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Pill identification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengidentifikasi pil: ' . $e->getMessage()
            ], 500);
        }
    }
}
