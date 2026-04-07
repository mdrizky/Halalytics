<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use App\Models\Consultation;
use App\Models\HalocodeMessage;
use App\Models\ExpertReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertController extends Controller
{
    public function index(Request $request)
    {
        $query = Expert::with('user:id_user,username,full_name,avatar_url')
            ->where('is_verified', true);

        if ($request->specialization) {
            $query->where('specialization', $request->specialization);
        }
        if ($request->boolean('online_only')) {
            $query->where('is_online', true);
        }

        $experts = $query->orderByDesc('rating')->paginate(20);
        return response()->json(['success' => true, 'data' => $experts]);
    }

    public function show($id)
    {
        $expert = Expert::with(['user:id_user,username,full_name,avatar_url', 'schedules', 'reviews.user:id_user,username'])
            ->findOrFail($id);
        return response()->json(['success' => true, 'data' => $expert]);
    }

    public function startConsultation(Request $request)
    {
        $request->validate([
            'expert_id' => 'required|exists:experts,id',
        ]);

        $expert = Expert::findOrFail($request->expert_id);

        $consultation = Consultation::create([
            'user_id'        => Auth::id(),
            'expert_id'      => $expert->id,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'amount'         => $expert->price_per_session,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konsultasi dibuat, menunggu pembayaran',
            'data'    => $consultation,
        ]);
    }

    public function sendMessage(Request $request, $consultationId)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $consultation = Consultation::where('id', $consultationId)
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhereHas('expert', fn($eq) => $eq->where('user_id', Auth::id()));
            })->firstOrFail();

        $message = HalocodeMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id'       => Auth::id(),
            'message'         => $request->message,
            'attachment_path' => $request->hasFile('attachment')
                ? $request->file('attachment')->store('chat_attachments', 'public') : null,
        ]);

        return response()->json(['success' => true, 'data' => $message]);
    }

    public function getMessages($consultationId)
    {
        $consultation = Consultation::where('id', $consultationId)
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhereHas('expert', fn($eq) => $eq->where('user_id', Auth::id()));
            })->firstOrFail();

        $messages = HalocodeMessage::where('consultation_id', $consultationId)
            ->with('sender:id_user,username,full_name,avatar_url')
            ->orderBy('created_at')
            ->get();

        // Mark as read
        HalocodeMessage::where('consultation_id', $consultationId)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function endConsultation($consultationId)
    {
        $consultation = Consultation::where('id', $consultationId)
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhereHas('expert', fn($eq) => $eq->where('user_id', Auth::id()));
            })->firstOrFail();

        $consultation->update(['status' => 'ended', 'ended_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Konsultasi selesai']);
    }

    public function submitReview(Request $request, $consultationId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $consultation = Consultation::where('id', $consultationId)
            ->where('user_id', Auth::id())
            ->where('status', 'ended')
            ->firstOrFail();

        $review = ExpertReview::updateOrCreate(
            ['consultation_id' => $consultationId, 'user_id' => Auth::id()],
            [
                'expert_id' => $consultation->expert_id,
                'rating'    => $request->rating,
                'review'    => $request->review,
            ]
        );

        // Update expert rating
        $expert = Expert::find($consultation->expert_id);
        $avgRating = ExpertReview::where('expert_id', $expert->id)->avg('rating');
        $totalReviews = ExpertReview::where('expert_id', $expert->id)->count();
        $expert->update(['rating' => round($avgRating, 2), 'total_reviews' => $totalReviews]);

        return response()->json(['success' => true, 'data' => $review]);
    }

    public function myConsultations()
    {
        $consultations = Consultation::where('user_id', Auth::id())
            ->with('expert.user:id_user,username,full_name,avatar_url')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $consultations]);
    }
}
