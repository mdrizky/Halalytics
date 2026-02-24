<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use App\Models\AiQueryLog;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LabAnalysisController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Analyze lab result from image or manual data
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|max:5120',
            'manual_data' => 'nullable|array',
            'test_date' => 'required|date'
        ]);

        try {
            $imageBase64 = null;
            $imageUrl = null;

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('lab_results', 'public');
                $imageUrl = url(Storage::url($path));
                $imageBase64 = base64_encode(file_get_contents($request->file('image')->path()));
            }

            $startTime = microtime(true);
            $aiResult = $this->geminiService->analyzeLabResult(
                $imageBase64,
                $request->input('manual_data', [])
            );
            $endTime = microtime(true);

            // Log AI Query
            AiQueryLog::create([
                'id_user' => auth()->user()->id_user,
                'query_type' => 'lab_analysis',
                'input_data' => ['has_image' => !!$imageBase64, 'manual_data' => $request->manual_data],
                'ai_response' => $aiResult,
                'processing_time' => ($endTime - $startTime) * 1000
            ]);

            // Save records
            $records = [];
            if (isset($aiResult['detected_tests'])) {
                foreach ($aiResult['detected_tests'] as $test) {
                    $records[] = LabResult::create([
                        'id_user' => auth()->user()->id_user,
                        'test_date' => $request->test_date,
                        'test_type' => $test['test_name'],
                        'value' => $test['value'],
                        'unit' => $test['unit'],
                        'status' => $test['status'],
                        'ai_analysis' => $test['interpretation'] ?? null,
                        'image_url' => $imageUrl
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $aiResult,
                'saved_records' => $records,
                'image_url' => $imageUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Lab analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menganalisis hasil lab: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user lab history
     */
    public function history()
    {
        $results = LabResult::where('id_user', auth()->user()->id_user)
            ->orderBy('test_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
