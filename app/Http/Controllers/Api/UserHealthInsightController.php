<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UserHealthInsightController extends Controller
{
    /**
     * Get a daily AI insight for the user.
     * Caches the insight for 24 hours based on the user's ID.
     */
    public function getDailyInsight(Request $request)
    {
        $user = $request->user();
        $cacheKey = 'daily_insight_user_' . $user->id;

        // Try to get from cache first (expires at midnight)
        $insight = Cache::remember($cacheKey, now()->endOfDay(), function () use ($user) {
            // Get user data (allergies, health targets if available)
            $allergies = $user->allergies ?? 'Tidak ada';
            $name = $user->name ?? 'Pengguna';
            
            // Build prompt for Gemini
            $prompt = "Tuliskan satu paragraf pendek (maksimal 3 kalimat) berisi saran atau wawasan kesehatan harian yang menyegarkan dan memotivasi untuk pengguna bernama $name. Bahas tentang menjaga gaya hidup halal dan sehat. Jika ada alergi: $allergies, berikan sedikit peringatan yang elegan. Buat nadanya positif, bersahabat, dan profesional bak konsultan kesehatan tingkat atas.";

            try {
                $apiKey = config('services.gemini.key');
                if (!$apiKey) {
                    return "Halo $name! Mari mulai hari ini dengan pola makan sehat dan teratur. Jangan lupa perhatikan asupan gizi seimbang yang terjamin kehalalannya.";
                }

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        return trim($result['candidates'][0]['content']['parts'][0]['text']);
                    }
                }
                
                // Fallback static insight
                return "Halo $name! Sempatkan waktu untuk membaca komposisi produk agar memastikan kehalalan dan keamanannya demi kesehatan jangka panjang.";
                
            } catch (\Exception $e) {
                return "Halo $name! Selalu jaga kesehatan Anda hari ini dengan mengonsumsi asupan bergizi.";
            }
        });

        return response()->json([
            'status' => 'success',
            'insight' => $insight
        ]);
    }

    /**
     * Get the user's overall health score (0-100).
     */
    public function getHealthScore(Request $request)
    {
        $user = $request->user();
        
        // Basic calculation based on activity (can be expanded later)
        // For now, simulate a semi-dynamic score between 65 and 95
        // We use user ID and current date to keep it stable within a day
        
        $baseScore = 70;
        $randomModifier = (int) substr(md5($user->id . date('Y-m-d')), 0, 2) % 25; // Random 0-24
        
        $score = $baseScore + $randomModifier;
        
        // Define level and color based on score
        if ($score >= 85) {
            $level = 'Sangat Baik';
            $color = '#22C55E'; // Green
        } elseif ($score >= 70) {
            $level = 'Baik';
            $color = '#0EA5E9'; // Blue
        } elseif ($score >= 50) {
            $level = 'Cukup';
            $color = '#F59E0B'; // Orange
        } else {
            $level = 'Perlu Perhatian';
            $color = '#EF4444'; // Red
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'score' => $score,
                'level' => $level,
                'color' => $color,
                'label' => 'Health Index'
            ]
        ]);
    }
}
