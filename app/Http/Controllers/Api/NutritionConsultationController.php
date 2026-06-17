<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\IntakeLog;
use App\Models\ScanHistory;
use App\Models\NutritionConsultation;
use App\Models\NutritionConsultationMessage;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NutritionConsultationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function indexNutritionist(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'ahli_gizi') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $uid = $user->id_user;
        $items = NutritionConsultation::query()
            ->where('nutritionist_id', $uid)
            ->with(['user:id_user,username,full_name,email,bmi,allergy,medical_history'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function indexAdmin(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $items = NutritionConsultation::query()
            ->with(['user:id_user,username,full_name', 'nutritionist:id_user,username,full_name'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $items]);
    }

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

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $c = NutritionConsultation::with(['user', 'nutritionist', 'messages'])->findOrFail($id);

        $isParticipant = (int) $c->user_id === (int) $user->id_user
            || (int) $c->nutritionist_id === (int) $user->id_user;

        if (!$isParticipant && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($user->role === 'ahli_gizi' && (int) $c->nutritionist_id !== (int) $user->id_user) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json(['success' => true, 'data' => $c]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string|in:expert,admin,support',
            'subject' => 'nullable|string|max:255',
            'nutritionist_id' => 'nullable|integer',
            'admin_id' => 'nullable|integer',
            'initial_message' => 'nullable|string|max:5000',
        ]);

        $userId = $request->user()->id_user;

        $c = NutritionConsultation::create([
            'type' => $validated['type'] ?? 'expert',
            'user_id' => $userId,
            'nutritionist_id' => $validated['nutritionist_id'] ?? null,
            'admin_id' => $validated['admin_id'] ?? null,
            'status' => 'open',
            'subject' => $validated['subject'] ?? 'Konsultasi gizi',
        ]);

        if (!empty($validated['initial_message'])) {
            $msg = NutritionConsultationMessage::create([
                'consultation_id' => $c->id,
                'sender_role' => 'user',
                'sender_user_id' => $userId,
                'body' => $validated['initial_message'],
            ]);

            broadcast(new NewNutritionMessage($msg))->toOthers();
        }

        return response()->json(['success' => true, 'data' => $c->load('messages')], 201);
    }

    public function storeMessage(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'nullable|string|max:8000',
            'sender_role' => 'required|in:user,nutritionist,admin,ai',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        if (empty($validated['body']) && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'Message body or attachment is required'], 422);
        }

        $user = $request->user();
        $c = NutritionConsultation::query()->findOrFail($id);

        $isParticipant = (int) $c->user_id === (int) $user->id_user
            || (int) $c->nutritionist_id === (int) $user->id_user;

        if (!$isParticipant && ($user->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($user->role === 'ahli_gizi' && (int) $c->nutritionist_id !== (int) $user->id_user) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($validated['sender_role'] === 'admin' && ($user->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Invalid sender_role'], 422);
        }

        if ($validated['sender_role'] === 'nutritionist' && ($user->role ?? '') !== 'ahli_gizi' && ($user->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Invalid sender_role'], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('consultations/attachments', $filename, 'public');
            $attachmentPath = $path;
        }

        $msg = NutritionConsultationMessage::create([
            'consultation_id' => $c->id,
            'sender_role' => $validated['sender_role'],
            'sender_user_id' => $user->id_user,
            'body' => $validated['body'] ?? '',
            'attachment_path' => $attachmentPath,
        ]);

        $c->touch();

        broadcast(new NewNutritionMessage($msg))->toOthers();

        try {
            $recipient = null;
            if ($validated['sender_role'] === 'user') {
                $recipient = $c->nutritionist ?? $c->admin;
            } else {
                $recipient = $c->user;
            }

            if ($recipient) {
                $this->notificationService->sendChatNotification(
                    $recipient,
                    $user->full_name ?? $user->username,
                    $msg->body ?: 'Mengirim file',
                    $c->id
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Chat notification failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'data' => $msg]);
    }
}
