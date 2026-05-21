<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiFeedback;
use App\Models\AiLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AILogsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AiLog::query()->latest();

        if ($request->filled('prompt_type')) {
            $query->where('prompt_type', $request->string('prompt_type'));
        }

        if ($request->filled('is_accurate')) {
            $query->where('is_accurate', filter_var($request->input('is_accurate'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json([
            'success' => true,
            'data' => $query->limit(100)->get(),
        ]);
    }

    public function submitFeedback(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'ai_log_id' => ['required', 'integer'],
            'is_accurate' => ['required', 'boolean'],
            'feedback_text' => ['nullable', 'string', 'max:1000'],
        ]);

        $feedback = AiFeedback::create([
            'user_id' => $request->user()?->id,
            'ai_log_id' => $payload['ai_log_id'],
            'is_accurate' => $payload['is_accurate'],
            'feedback_text' => $payload['feedback_text'] ?? null,
        ]);

        AiLog::query()->whereKey($payload['ai_log_id'])->update([
            'is_accurate' => $payload['is_accurate'],
            'feedback_text' => $payload['feedback_text'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $feedback], 201);
    }

    public function analytics(): JsonResponse
    {
        $topIntents = AiLog::query()
            ->select('prompt_type', DB::raw('count(*) as total'))
            ->groupBy('prompt_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topScannedProducts = DB::table('scan_histories')
            ->select('product_name', DB::raw('count(*) as total'))
            ->groupBy('product_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $accuracyTrend = AiFeedback::query()
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('AVG(CASE WHEN is_accurate = 1 THEN 1 ELSE 0 END) as accuracy'))
            ->groupBy('day')
            ->orderBy('day')
            ->limit(14)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'top_intents' => $topIntents,
                'top_scanned_products' => $topScannedProducts,
                'accuracy_trend' => $accuracyTrend,
            ],
        ]);
    }

}
