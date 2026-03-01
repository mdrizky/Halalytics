<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HealthMetricController extends Controller
{
    /**
     * Record a new health metric
     */
    public function store(Request $request)
    {
        $request->validate([
            'metric_type' => 'required|in:weight,blood_pressure,blood_sugar,cholesterol,health_diary',
            'value' => 'required|string',
            'recorded_at' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        try {
            $record = HealthTracking::create([
                'id_user' => auth()->user()->id_user,
                'metric_type' => $request->metric_type,
                'value' => $request->value,
                'recorded_at' => $request->recorded_at,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kesehatan berhasil dicatat.',
                'data' => $record
            ]);

        } catch (\Exception $e) {
            Log::error('Health metric creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get history for a specific metric (for charts)
     */
    public function history(Request $request)
    {
        $request->validate([
            'metric_type' => 'required|in:weight,blood_pressure,blood_sugar,cholesterol,health_diary',
            'limit' => 'nullable|integer|max:100'
        ]);

        $results = HealthTracking::where('id_user', auth()->user()->id_user)
            ->where('metric_type', $request->metric_type)
            ->orderBy('recorded_at', 'asc')
            ->take($request->input('limit', 20))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get dedicated health diary entries (latest first)
     */
    public function diary(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|max:100'
        ]);

        $results = HealthTracking::where('id_user', auth()->user()->id_user)
            ->where('metric_type', 'health_diary')
            ->orderBy('recorded_at', 'desc')
            ->take($request->input('limit', 30))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get summary of all metrics
     */
    public function summary()
    {
        $metrics = ['weight', 'blood_pressure', 'blood_sugar', 'cholesterol', 'health_diary'];
        $summary = [];

        foreach ($metrics as $metric) {
            $latest = HealthTracking::where('id_user', auth()->user()->id_user)
                ->where('metric_type', $metric)
                ->orderBy('recorded_at', 'desc')
                ->first();
            
            if ($latest) {
                $summary[$metric] = $latest;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * AI Health Analysis based on profile
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'age' => 'required',
            'height' => 'required',
            'weight' => 'required'
        ]);

        try {
            $gemini = app(\App\Services\GeminiService::class);
            
            $prompt = "Berikan analisis kesehatan singkat untuk profil:
                Usia: {$request->age}, Tinggi: {$request->height}cm, Berat: {$request->weight}kg.
                Berikan JSON: {bmi: angka, status: string, recommendations: [string], risks: [string]}";

            $result = $gemini->generateText($prompt);
            
            if (is_string($result)) {
                $decoded = json_decode($result, true);
                if ($decoded) $result = $decoded;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
