<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, $consultationId)
    {
        $consultation = $this->resolveConsultation($request, $consultationId);

        if (! $consultation) {
            return $this->errorResponse('Konsultasi tidak ditemukan atau tidak dapat diakses.', 403);
        }

        $consultation->messages()
            ->where('sender_id', '!=', $request->user()->id_user)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $consultation->messages()
            ->with('sender:id_user,full_name,username,image,avatar_url')
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $message) => $this->messagePayload($message));

        return $this->successResponse($messages, 'Pesan konsultasi berhasil diambil.');
    }

    public function store(Request $request, $consultationId)
    {
        $consultation = $this->resolveConsultation($request, $consultationId);

        if (! $consultation) {
            return $this->errorResponse('Konsultasi tidak ditemukan atau tidak dapat diakses.', 403);
        }

        if (! in_array($consultation->status, ['paid', 'active'], true)) {
            return $this->errorResponse('Sesi chat belum aktif.', 422);
        }

        $validated = $request->validate([
            'message' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('consultations', 'public')
            : null;

        $message = Message::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $request->user()->id_user,
            'message' => $validated['message'] ?? '',
            'attachment_path' => $attachmentPath,
            'is_read' => false,
        ]);

        $message->load('sender:id_user,full_name,username,image,avatar_url');

        broadcast(new NewMessageSent($message))->toOthers();

        return $this->successResponse($this->messagePayload($message), 'Pesan berhasil dikirim.', 201);
    }

    private function resolveConsultation(Request $request, int|string $consultationId): ?Consultation
    {
        $consultation = Consultation::with(['expert.user', 'user'])->find($consultationId);

        if (! $consultation) {
            return null;
        }

        $userId = (int) $request->user()->id_user;
        $isUser = (int) $consultation->user_id === $userId;
        $isExpert = (int) $consultation->expert?->user_id === $userId;

        return $isUser || $isExpert ? $consultation : null;
    }

    private function messagePayload(Message $message): array
    {
        return [
            'id' => $message->id,
            'consultation_id' => $message->consultation_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->full_name ?? $message->sender?->username,
            'message' => $message->message,
            'attachment_path' => $message->attachment_path,
            'is_read' => (bool) $message->is_read,
            'created_at' => optional($message->created_at)->toISOString(),
        ];
    }
}
