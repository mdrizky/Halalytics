<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NutritionScan;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NutritionScanController extends Controller
{
    private $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function scan(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'image_base64' => 'required'
        ]);

        $imageData = base64_decode($request->image_base64);
        if ($imageData === false) {
             return response()->json(['success' => false, 'message' => 'Invalid base64 Data'], 400);
        }
        
        $fileName = 'scans/' . time() . '.jpg';
        Storage::disk('public')->put($fileName, $imageData);

        // Analysis
        $prompt = "Baca teks komposisi gambar ini. Tentukan status halalnya (Halal/Syubhat/Haram). Cari alkohol, karmin, gelatin babi. Berikan JSON format: {'halal_status': '...', 'ingredients_concern': [...], 'kalori': ..., 'gula': ..., 'health_score': 85}";
        
        $resultText = $this->gemini->processImagePrompt($request->image_base64, $prompt);
        // Clean JSON string and decode it
        // Remove markdown
        $resultText = preg_replace('/```json\s*|\s*```/', '', $resultText);
        $resultText = trim($resultText);
        
        $aiResult = json_decode($resultText, true);

        if (!$aiResult) {
            $aiResult = [
               'halal_status' => 'Tidak Diketahui',
               'health_score' => 0,
               'ingredients_concern' => [],
               'kalori' => 0,
               'gula' => 0
            ];
        }

        $scan = NutritionScan::create([
            'id_user' => $request->user_id,
            'product_image_path' => $fileName,
            'ai_nutrition_analysis' => json_encode($aiResult),
            'halal_status' => $aiResult['halal_status'] ?? 'Tidak Diketahui',
            'health_score' => $aiResult['health_score'] ?? 0
        ]);

        if ($scan->halal_status === 'Haram') {
            broadcast(new \App\Events\HaramProductDetected($scan));
        }

        return response()->json([
            'success' => true,
            'data' => $scan
        ], 201);
    }
}
