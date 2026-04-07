<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Expert;
use App\Models\ExpertReview;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    public function index(Request $request)
    {
        $experts = Expert::query()
            ->with('user:id_user,username,full_name,image,avatar_url')
            ->where('is_verified', true)
            ->when($request->filled('specialization'), fn ($query) => $query->where('specialization', $request->input('specialization')))
            ->when($request->boolean('online_only'), fn ($query) => $query->where('is_online', true))
            ->orderByDesc('is_online')
            ->orderByDesc('rating')
            ->get()
            ->map(fn (Expert $expert) => $this->expertPayload($expert));

        return $this->successResponse($experts, 'Daftar pakar berhasil diambil.');
    }

    public function show($id)
    {
        $expert = Expert::query()
            ->with([
                'user:id_user,username,full_name,image,avatar_url',
                'schedules',
                'reviews.user:id_user,username,full_name,image,avatar_url',
            ])
            ->findOrFail($id);

        $payload = $this->expertPayload($expert);
        $payload['schedules'] = $expert->schedules
            ->where('is_active', true)
            ->sortBy(fn ($schedule) => sprintf('%d-%s', $schedule->day_of_week, $schedule->start_time))
            ->values()
            ->map(fn ($schedule) => [
                'id' => $schedule->id,
                'day_of_week' => (int) $schedule->day_of_week,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'is_active' => (bool) $schedule->is_active,
            ])
            ->all();
        $payload['reviews'] = $expert->reviews
            ->sortByDesc('created_at')
            ->values()
            ->map(fn (ExpertReview $review) => [
                'id' => $review->id,
                'user_id' => $review->user_id,
                'user_name' => $review->user?->full_name ?? $review->user?->username,
                'rating' => (int) $review->rating,
                'review' => $review->review,
                'created_at' => optional($review->created_at)->toISOString(),
            ])
            ->all();

        return $this->successResponse($payload, 'Detail pakar berhasil diambil.');
    }

    public function toggleOnline(Request $request)
    {
        $expert = Expert::firstOrCreate(
            ['user_id' => $request->user()->id_user],
            [
                'specialization' => 'Konsultan Umum',
                'bio' => null,
                'price_per_session' => 0,
                'is_verified' => false,
                'is_online' => false,
            ]
        );

        $expert->update([
            'is_online' => ! $expert->is_online,
        ]);

        return $this->successResponse([
            'is_online' => (bool) $expert->fresh()->is_online,
        ], 'Status online pakar berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'price_per_session' => 'required|integer|min:0',
            'certificate' => 'nullable|file|max:5120',
        ]);

        $expert = Expert::firstOrCreate(
            ['user_id' => $request->user()->id_user],
            [
                'specialization' => $validated['specialization'],
                'bio' => $validated['bio'] ?? null,
                'price_per_session' => $validated['price_per_session'],
                'is_verified' => false,
                'is_online' => false,
            ]
        );

        $payload = [
            'specialization' => $validated['specialization'],
            'bio' => $validated['bio'] ?? null,
            'price_per_session' => $validated['price_per_session'],
        ];

        if ($request->hasFile('certificate')) {
            $payload['certificate_path'] = $request->file('certificate')->store('experts/certificates', 'public');
            $payload['is_verified'] = false;
        }

        $expert->update($payload);

        return $this->successResponse(
            $this->expertPayload($expert->fresh('user')),
            'Profil pakar berhasil diperbarui.'
        );
    }

    public function startConsultation(Request $request)
    {
        return app(ConsultationController::class)->store($request);
    }

    public function callback(Request $request)
    {
        return app(ConsultationController::class)->callback($request);
    }

    public function sendMessage(Request $request, $consultationId)
    {
        return app(MessageController::class)->store($request, $consultationId);
    }

    public function getMessages(Request $request, $consultationId)
    {
        return app(MessageController::class)->index($request, $consultationId);
    }

    public function endConsultation(Request $request, $consultationId)
    {
        return app(ConsultationController::class)->end($request, $consultationId);
    }

    public function submitReview(Request $request, $consultationId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $consultation = Consultation::query()
            ->where('id', $consultationId)
            ->where('user_id', $request->user()->id_user)
            ->where('status', 'ended')
            ->firstOrFail();

        $review = ExpertReview::updateOrCreate(
            [
                'consultation_id' => $consultation->id,
                'user_id' => $request->user()->id_user,
                'expert_id' => $consultation->expert_id,
            ],
            [
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null,
            ]
        );

        $expert = Expert::findOrFail($consultation->expert_id);
        $expert->update([
            'rating' => round((float) ExpertReview::where('expert_id', $expert->id)->avg('rating'), 2),
            'total_reviews' => ExpertReview::where('expert_id', $expert->id)->count(),
        ]);

        return $this->successResponse([
            'id' => $review->id,
            'consultation_id' => $review->consultation_id,
            'expert_id' => $review->expert_id,
            'user_id' => $review->user_id,
            'rating' => (int) $review->rating,
            'review' => $review->review,
            'created_at' => optional($review->created_at)->toISOString(),
        ], 'Ulasan konsultasi berhasil dikirim.');
    }

    public function myConsultations(Request $request)
    {
        return app(ConsultationController::class)->history($request);
    }

    private function expertPayload(Expert $expert): array
    {
        return [
            'id' => $expert->id,
            'user_id' => $expert->user_id,
            'name' => $expert->user?->full_name ?? $expert->user?->username ?? 'Pakar',
            'photo_url' => $expert->user?->avatar_url ?? $expert->user?->image,
            'specialization' => $expert->specialization,
            'bio' => $expert->bio,
            'is_verified' => (bool) $expert->is_verified,
            'is_online' => (bool) $expert->is_online,
            'price_per_session' => (int) $expert->price_per_session,
            'rating' => (float) $expert->rating,
            'total_reviews' => (int) $expert->total_reviews,
        ];
    }
}
