<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyLog;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    private $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function triggerEmergency(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'emergency_type' => 'required|string', // e.g., "Tersedak"
        ]);

        // Prioritas tinggi: panggil AI secara kilat
        $prompt = "Ini darurat! Seseorang mengalami: " . $request->emergency_type . " . Berikan Maksimal 3 langkah P3K (Pertolongan Pertama) yang sangat singkat dan jelas. Format kembalian wajib JSON array, contoh: [\"Langkah 1...\", \"Langkah 2...\"]";
        
        $aiResultRaw = $this->gemini->generateText($prompt);
        // Clean JSON
        $aiResultRaw = preg_replace('/```json\s*|\s*```/', '', $aiResultRaw);
        $aiResultRaw = trim($aiResultRaw);
        $steps = json_decode($aiResultRaw, true);

        if (!$steps) {
            $steps = ["Segera hubungi 119 atau rumah sakit terdekat.", "Pastikan pasien tetap tenang."];
        }

        $emergencyLog = EmergencyLog::create([
            'id_user' => $request->user_id,
            'emergency_type' => $request->emergency_type,
            'location_latitude' => $request->latitude,
            'location_longitude' => $request->longitude,
            'ai_guidance' => json_encode($steps)
        ]);

        // Real-time Update to Admin Dashboard (Sirene Merah)
        broadcast(new \App\Events\EmergencyTriggered($emergencyLog));

        return response()->json([
            'success' => true,
            'guidance' => $steps,
            'data' => $emergencyLog
        ], 201);
    }
}
