<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ConsultationSession;
use App\Models\Specialist;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function specialists()
    {
        $specialists = Specialist::where('is_available', true)
            ->orderByDesc('is_online')
            ->orderByDesc('rating')
            ->get();

        return response()->json($specialists);
    }

    public function startSession(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:specialists,id',
            'topic' => 'nullable|string|max:200',
        ]);

        $userId = $request->user()->id_user;

        $activeSession = ConsultationSession::with(['specialist', 'messages'])
            ->where('user_id', $userId)
            ->whereIn('status', ['waiting', 'active'])
            ->latest()
            ->first();

        if ($activeSession) {
            return response()->json([
                'session' => $activeSession,
            ]);
        }

        $session = ConsultationSession::create([
            'user_id' => $userId,
            'specialist_id' => $request->input('specialist_id'),
            'topic' => $request->input('topic'),
            'status' => 'waiting',
        ]);

        return response()->json([
            'session' => $session->load('specialist'),
        ], 201);
    }

    public function sendMessage(Request $request, ConsultationSession $session)
    {
        $participant = $this->resolveParticipant($request, $session);

        if (!$participant['authorized']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($session->status, ['waiting', 'active'])) {
            return response()->json(['error' => 'Sesi sudah berakhir'], 400);
        }

        $request->validate([
            'message' => 'required_without:file|string|max:1000',
            'message_type' => 'nullable|in:text,image,file',
            'file' => 'nullable|file|max:5120',
        ]);

        $fileUrl = null;

        if ($request->hasFile('file')) {
            $storedPath = $request->file('file')->store('chat_files', 'public');
            $fileUrl = asset('storage/' . $storedPath);
        }

        $message = ChatMessage::create([
            'session_id' => $session->id,
            'sender_id' => $request->user()->id_user,
            'sender_type' => $participant['sender_type'],
            'message' => $request->input('message', ''),
            'message_type' => $request->input('message_type', $fileUrl ? 'file' : 'text'),
            'file_url' => $fileUrl,
            'is_read' => false,
        ]);

        if ($session->status === 'waiting') {
            $session->update([
                'status' => 'active',
                'started_at' => now(),
            ]);
        }

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json([
            'message' => $message->load('sender'),
        ]);
    }

    public function getMessages(Request $request, ConsultationSession $session)
    {
        $participant = $this->resolveParticipant($request, $session);

        if (!$participant['authorized']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $session->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        $session->messages()
            ->where('sender_type', '!=', $participant['sender_type'])
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'session' => $session->load('specialist'),
            'messages' => $messages,
        ]);
    }

    public function endSession(Request $request, ConsultationSession $session)
    {
        $participant = $this->resolveParticipant($request, $session);

        if (!$participant['authorized']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($session->status, ['waiting', 'active'])) {
            return response()->json(['message' => 'Sesi sudah selesai'], 200);
        }

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $session->specialist()->increment('total_consultations');

        return response()->json(['message' => 'Sesi berakhir']);
    }

    private function resolveParticipant(Request $request, ConsultationSession $session): array
    {
        $authUserId = $request->user()->id_user;

        if ((int) $session->user_id === (int) $authUserId) {
            return [
                'authorized' => true,
                'sender_type' => 'user',
            ];
        }

        if ($session->specialist && (int) $session->specialist->user_id === (int) $authUserId) {
            return [
                'authorized' => true,
                'sender_type' => 'specialist',
            ];
        }

        return [
            'authorized' => false,
            'sender_type' => null,
        ];
    }
}
