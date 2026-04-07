<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\NutritionGoal;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NutritionController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function logMeal(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|max:10240',
            'meal_type' => 'required|in:sarapan,makan_siang,makan_malam,camilan',
        ]);

        $imagePath = $request->file('image')->store('meal_logs/' . $request->user()->id_user, 'public');
        $imageData = file_get_contents(Storage::disk('public')->path($imagePath));
        $base64    = base64_encode($imageData);

        try {
            $analysis = $this->gemini->analyzeFood($base64);

            $log = DailyLog::create([
                'user_id'         => $request->user()->id_user,
                'meal_type'       => $request->meal_type,
                'food_items'      => $analysis['food_items'] ?? [],
                'total_calories'  => $analysis['total_calories'] ?? 0,
                'total_carbs'     => $analysis['total_carbs'] ?? 0,
                'total_protein'   => $analysis['total_protein'] ?? 0,
                'total_fat'       => $analysis['total_fat'] ?? 0,
                'image_path'      => $imagePath,
                'gemini_response' => json_encode($analysis),
                'logged_at'       => now()->toDateString(),
            ]);

            return $this->successResponse(
                $this->logPayload($log),
                'Makanan berhasil dicatat dan dianalisis!'
            );
        } catch (\Exception $e) {
            Storage::disk('public')->delete($imagePath);
            return $this->errorResponse('Gagal menganalisis: ' . $e->getMessage(), 500);
        }
    }

    public function getDailyLog(Request $request)
    {
        $date = $request->date ?? now()->toDateString();
        $logs = DailyLog::where('user_id', $request->user()->id_user)
            ->where('logged_at', $date)->get();
        $goal = NutritionGoal::where('user_id', $request->user()->id_user)->first();

        return $this->successResponse([
            'date'           => $date,
            'logs'           => $logs->map(fn (DailyLog $log) => $this->logPayload($log))->values(),
            'total_calories' => (int) $logs->sum('total_calories'),
            'total_carbs'    => (float) $logs->sum('total_carbs'),
            'total_protein'  => (float) $logs->sum('total_protein'),
            'total_fat'      => (float) $logs->sum('total_fat'),
            'goal'           => $goal,
        ], 'Ringkasan nutrisi harian berhasil diambil.');
    }

    public function getHistory(Request $request)
    {
        $days = $request->days ?? 30;
        $history = DailyLog::where('user_id', $request->user()->id_user)
            ->where('logged_at', '>=', now()->subDays($days)->toDateString())
            ->get()
            ->groupBy('logged_at')
            ->map(fn($logs, $date) => [
                'date'           => $date,
                'total_calories' => $logs->sum('total_calories'),
                'total_carbs'    => $logs->sum('total_carbs'),
                'total_protein'  => $logs->sum('total_protein'),
                'total_fat'      => $logs->sum('total_fat'),
                'meal_count'     => $logs->count(),
            ])->values();

        return $this->successResponse($history, 'Riwayat nutrisi berhasil diambil.');
    }

    public function setGoals(Request $request)
    {
        $request->validate([
            'daily_calories' => 'required|integer|min:500|max:5000',
            'daily_carbs'    => 'required|numeric',
            'daily_protein'  => 'required|numeric',
            'daily_fat'      => 'required|numeric',
            'goal_type'      => 'required|in:diet,maintain,bulking',
        ]);

        $goal = NutritionGoal::updateOrCreate(
            ['user_id' => $request->user()->id_user],
            $request->only(['daily_calories', 'daily_carbs', 'daily_protein', 'daily_fat', 'goal_type'])
        );

        return $this->successResponse($goal, 'Target nutrisi disimpan.');
    }

    public function getGoals(Request $request)
    {
        $goal = NutritionGoal::where('user_id', $request->user()->id_user)->first();
        return $this->successResponse($goal, 'Target nutrisi berhasil diambil.');
    }

    private function logPayload(DailyLog $log): array
    {
        $gemini = json_decode($log->gemini_response ?? '{}', true);

        return [
            'id' => $log->id,
            'meal_type' => $log->meal_type,
            'food_items' => $log->food_items ?? [],
            'total_calories' => (int) $log->total_calories,
            'total_carbs' => (float) $log->total_carbs,
            'total_protein' => (float) $log->total_protein,
            'total_fat' => (float) $log->total_fat,
            'image_path' => $log->image_path ? Storage::disk('public')->url($log->image_path) : null,
            'analysis_note' => $gemini['analysis_note'] ?? null,
            'logged_at' => (string) $log->logged_at,
        ];
    }
}
