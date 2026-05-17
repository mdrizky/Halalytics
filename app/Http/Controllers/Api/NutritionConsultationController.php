<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NutritionConsultation;
use App\Models\NutritionConsultationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionConsultationController extends Controller
{
    /** Daftar konsultasi untuk ahli gizi yang login */
    public function indexNutritionist(Request $request): JsonResponse
    {
        $uid = $request->user()->id_user;
        $items = NutritionConsultation::query()
            ->where('nutritionist_id', $uid)
            ->with(['user:id_user,username,full_name,email,bmi,allergy,medical_history'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $items]);
    }

    /** Riwayat konsultasi milik user biasa */
    public function mine(Request $request): JsonResponse
    {
        $uid = $request->user()->id_user;
        $items = NutritionConsultation::query()
            ->where('user_id', $uid)
            ->with(['nutritionist:id_user,username,full_name'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $items]);
    }

    /** User membuka konsultasi baru (nutritionist_id opsional; bisa ditugaskan admin) */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'nutritionist_id' => 'nullable|integer',
            'initial_message' => 'nullable|string|max:5000',
        ]);

        $userId = $request->user()->id_user;

        $c = NutritionConsultation::create([
            'user_id' => $userId,
            'nutritionist_id' => $validated['nutritionist_id'] ?? null,
            'status' => 'open',
            'subject' => $validated['subject'] ?? 'Konsultasi gizi',
        ]);

        if (! empty($validated['initial_message'])) {
            NutritionConsultationMessage::create([
                'consultation_id' => $c->id,
                'sender_role' => 'user',
                'sender_user_id' => $userId,
                'body' => $validated['initial_message'],
            ]);
        }

        return response()->json(['success' => true, 'data' => $c->load('messages')], 201);
    }

    public function storeMessage(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:8000',
            'sender_role' => 'required|in:user,nutritionist,ai',
        ]);

        $user = $request->user();
        $c = NutritionConsultation::query()->findOrFail($id);

        $isParticipant = (int) $c->user_id === (int) $user->id_user
            || (int) $c->nutritionist_id === (int) $user->id_user;

        if (! $isParticipant && ($user->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($validated['sender_role'] === 'nutritionist' && ($user->role ?? '') !== 'nutritionist' && ($user->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Invalid sender_role'], 422);
        }

        if ($validated['sender_role'] === 'user' && (int) $c->user_id !== (int) $user->id_user) {
            return response()->json(['success' => false, 'message' => 'Invalid sender'], 422);
        }

        $msg = NutritionConsultationMessage::create([
            'consultation_id' => $c->id,
            'sender_role' => $validated['sender_role'],
            'sender_user_id' => $user->id_user,
            'body' => $validated['body'],
        ]);

        $c->touch();

        return response()->json(['success' => true, 'data' => $msg]);
    }
}
