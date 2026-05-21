<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiFeedback;
use Illuminate\Http\JsonResponse;

class AITrainingController extends Controller
{
    public function dataset(): JsonResponse
    {
        $items = AiFeedback::query()->latest()->limit(200)->get(['ai_log_id', 'is_accurate', 'feedback_text', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'training_feedbacks' => $items,
                'note' => 'Gunakan feedback ini untuk iterasi prompt dan rules AI secara berkala.',
            ],
        ]);
    }
}
