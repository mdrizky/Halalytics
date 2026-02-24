<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use App\Models\LabParameter;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabResultController extends Controller
{
    private $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function uploadAndAnalyze(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'lab_image_base64' => 'required'
        ]);

        $imageData = base64_decode($request->lab_image_base64);
        if ($imageData === false) {
             return response()->json(['success' => false, 'message' => 'Invalid base64 Data'], 400);
        }

        $fileName = 'lab_results/' . time() . '.jpg';
        Storage::disk('public')->put($fileName, $imageData);

        $prompt = "Analisis hasil laboratorium dari gambar ini. Identifikasi tes yang ada, nilai, rujukan, dan interpretasi medis awam. Format JSON:\n{ 'ringkasan_kondisi': '...', 'saran_gaya_hidup': '...', 'urgensi': 'Ya/Tidak', 'poin_perhatian': [ {'parameter': '...', 'nilai': ..., 'rujukan': '...', 'status': 'Normal/Tinggi/Rendah', 'penjelasan': '...'} ] }";
        
        $resultText = $this->gemini->processImagePrompt($request->lab_image_base64, $prompt);
        $resultText = preg_replace('/```json\s*|\s*```/', '', $resultText);
        $resultText = trim($resultText);
        
        $aiAnalysis = json_decode($resultText, true);

        if (!$aiAnalysis) {
            return response()->json(['success' => false, 'message' => 'Gagal menganalisa dari AI'], 500);
        }

        $healthStatus = 'Normal';
        if (isset($aiAnalysis['urgensi']) && strtolower($aiAnalysis['urgensi']) === 'ya') {
             $healthStatus = 'Bahaya';
        } else {
             // Cek jika ada status Tinggi/Rendah
             if (isset($aiAnalysis['poin_perhatian'])) {
                 foreach ($aiAnalysis['poin_perhatian'] as $param) {
                     if (isset($param['status']) && $param['status'] !== 'Normal') {
                         $healthStatus = 'Perhatian';
                         break;
                     }
                 }
             }
        }

        $labResult = LabResult::create([
            'id_user' => $request->user_id,
            'image_url' => $fileName,
            'test_date' => $request->test_date ?? now(),
            'test_type' => $request->lab_type ?? 'Pemeriksaan Lab',
            'ai_analysis' => json_encode([
                'ringkasan_kondisi' => $aiAnalysis['ringkasan_kondisi'] ?? '',
                'saran_gaya_hidup' => $aiAnalysis['saran_gaya_hidup'] ?? '',
                'urgensi' => $aiAnalysis['urgensi'] ?? 'Tidak'
            ]),
            'status' => strtolower($healthStatus) === 'bahaya' ? 'high' : (strtolower($healthStatus) === 'perhatian' ? 'low' : 'normal'), // Mapping the enum for DB
        ]);

        if (isset($aiAnalysis['poin_perhatian']) && is_array($aiAnalysis['poin_perhatian'])) {
            foreach ($aiAnalysis['poin_perhatian'] as $param) {
                LabParameter::create([
                    'lab_result_id' => $labResult->id,
                    'parameter_name' => $param['parameter'] ?? 'Unknown',
                    'user_value' => floatval($param['nilai'] ?? 0),
                    'normal_range' => $param['rujukan'] ?? '-',
                    'status' => isset($param['status']) ? ucfirst($param['status']) : 'Normal',
                    'explanation' => $param['penjelasan'] ?? ''
                ]);
            }
        }

        if ($healthStatus === 'Bahaya') {
            broadcast(new \App\Events\CriticalLabResultDetected($labResult));
        }

        $labResult->load('parameters'); // Assuming relationship is defined

        return response()->json([
            'success' => true,
            'data' => $labResult,
            'health_status' => $healthStatus
        ], 201);
    }
}
