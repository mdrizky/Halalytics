<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiFeedback;
use App\Models\AiLog;
use Illuminate\Http\Request;

class AdminAiLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AiLog::query()
            ->when($request->prompt_type, fn ($q, $t) => $q->where('prompt_type', $t))
            ->orderByDesc('created_at')
            ->paginate(30);

        return response()->json(['success' => true, 'data' => $logs]);
    }

    public function stats()
    {
        $today = AiLog::query()->whereDate('created_at', today());
        $feedbacks = AiFeedback::query();

        return response()->json([
            'success' => true,
            'data' => [
                'requests_today' => $today->count(),
                'avg_response_ms' => (int) $today->avg('response_time_ms'),
                'accurate_count' => (clone $feedbacks)->where('is_accurate', true)->count(),
                'inaccurate_count' => (clone $feedbacks)->where('is_accurate', false)->count(),
            ],
        ]);
    }

    public function feedback(Request $request)
    {
        $validated = $request->validate([
            'ai_log_id' => 'required|exists:ai_logs,id',
            'is_accurate' => 'required|boolean',
            'feedback_text' => 'nullable|string|max:2000',
        ]);

        $userId = $request->user()->id_user ?? $request->user()->id;

        $feedback = AiFeedback::create([
            'user_id' => $userId,
            'ai_log_id' => $validated['ai_log_id'],
            'is_accurate' => $validated['is_accurate'],
            'feedback_text' => $validated['feedback_text'] ?? null,
        ]);

        AiLog::where('id', $validated['ai_log_id'])->update([
            'is_accurate' => $validated['is_accurate'],
            'feedback_text' => $validated['feedback_text'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $feedback]);
    }
}
