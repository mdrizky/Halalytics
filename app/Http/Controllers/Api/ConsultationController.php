<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Expert;
use App\Models\ExpertWallet;
use App\Models\WalletTransaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function __construct(private readonly MidtransService $midtransService)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expert_id' => 'required|exists:experts,id',
        ]);

        $expert = Expert::with('user')->findOrFail($validated['expert_id']);

        if (! $expert->is_verified) {
            return $this->errorResponse('Pakar belum terverifikasi.', 422);
        }

        $activeConsultation = Consultation::query()
            ->with(['expert.user'])
            ->where('user_id', $request->user()->id_user)
            ->where('expert_id', $expert->id)
            ->whereIn('status', ['pending', 'paid', 'active'])
            ->latest()
            ->first();

        if ($activeConsultation) {
            return $this->successResponse(
                $this->consultationPayload($activeConsultation),
                'Masih ada konsultasi aktif untuk pakar ini.'
            );
        }

        $consultation = Consultation::create([
            'user_id' => $request->user()->id_user,
            'expert_id' => $expert->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'amount' => $expert->price_per_session,
        ]);

        $payment = $this->midtransService->createConsultationPayment(
            $consultation,
            $request->user(),
            $expert
        );

        $consultation->update([
            'payment_token' => $payment['token'] ?? null,
            'payment_status' => ($payment['is_mock'] ?? false) ? 'paid' : $consultation->payment_status,
            'status' => ($payment['is_mock'] ?? false) ? 'paid' : $consultation->status,
        ]);

        $data = $this->consultationPayload($consultation->fresh('expert.user'));
        $data['payment_redirect_url'] = $payment['redirect_url'] ?? null;
        $data['payment_order_id'] = $payment['order_id'] ?? null;
        $data['payment_is_mock'] = $payment['is_mock'] ?? false;

        return $this->successResponse($data, 'Konsultasi berhasil dibuat.', 201);
    }

    public function callback(Request $request)
    {
        $payload = $request->all();

        if (! $this->midtransService->isValidSignature($payload)) {
            return $this->errorResponse('Signature Midtrans tidak valid.', 403);
        }

        $consultationId = $this->midtransService->extractConsultationId($payload['order_id'] ?? null);

        if (! $consultationId) {
            return $this->errorResponse('Order ID tidak dikenali.', 422);
        }

        $consultation = Consultation::findOrFail($consultationId);
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (in_array($transactionStatus, ['capture', 'settlement'], true)
            && ($fraudStatus === null || $fraudStatus === 'accept')
        ) {
            $consultation->update([
                'payment_status' => 'paid',
                'status' => 'paid',
            ]);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            $consultation->update([
                'payment_status' => 'unpaid',
                'status' => 'cancelled',
            ]);
        } elseif ($transactionStatus === 'refund') {
            $consultation->update([
                'payment_status' => 'refunded',
                'status' => 'cancelled',
            ]);
        }

        return $this->successResponse($this->consultationPayload($consultation->fresh('expert.user')), 'Callback pembayaran diproses.');
    }

    public function start(Request $request, $id)
    {
        $consultation = Consultation::with('expert.user')->findOrFail($id);

        if ((int) $consultation->expert?->user_id !== (int) $request->user()->id_user) {
            return $this->errorResponse('Anda tidak berhak memulai konsultasi ini.', 403);
        }

        if ($consultation->payment_status !== 'paid') {
            return $this->errorResponse('Konsultasi belum dibayar.', 422);
        }

        $consultation->update([
            'status' => 'active',
            'started_at' => $consultation->started_at ?? now(),
        ]);

        return $this->successResponse($this->consultationPayload($consultation->fresh('expert.user')), 'Sesi konsultasi dimulai.');
    }

    public function end(Request $request, $id)
    {
        $consultation = Consultation::with('expert.user')->findOrFail($id);

        $isUser = (int) $consultation->user_id === (int) $request->user()->id_user;
        $isExpert = (int) $consultation->expert?->user_id === (int) $request->user()->id_user;

        if (! $isUser && ! $isExpert) {
            return $this->errorResponse('Anda tidak berhak mengakhiri konsultasi ini.', 403);
        }

        DB::transaction(function () use ($consultation) {
            if ($consultation->status !== 'ended') {
                $consultation->update([
                    'status' => 'ended',
                    'ended_at' => $consultation->ended_at ?? now(),
                ]);
            }

            if ($consultation->payment_status !== 'paid') {
                return;
            }

            $wallet = ExpertWallet::firstOrCreate(
                ['expert_id' => $consultation->expert_id],
                ['balance' => 0, 'total_earned' => 0]
            );

            $referenceId = 'consultation:' . $consultation->id;
            $alreadyReleased = WalletTransaction::where('expert_wallet_id', $wallet->id)
                ->where('reference_id', $referenceId)
                ->exists();

            if ($alreadyReleased) {
                return;
            }

            $wallet->increment('balance', $consultation->amount);
            $wallet->increment('total_earned', $consultation->amount);

            WalletTransaction::create([
                'expert_wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $consultation->amount,
                'description' => 'Pencairan dana konsultasi #' . $consultation->id,
                'reference_id' => $referenceId,
            ]);
        });

        return $this->successResponse($this->consultationPayload($consultation->fresh('expert.user')), 'Sesi konsultasi diakhiri.');
    }

    public function history(Request $request)
    {
        $history = Consultation::query()
            ->with(['expert.user'])
            ->where('user_id', $request->user()->id_user)
            ->latest()
            ->get()
            ->map(fn (Consultation $consultation) => $this->consultationPayload($consultation));

        return $this->successResponse($history, 'Riwayat konsultasi berhasil diambil.');
    }

    public function expertQueue(Request $request)
    {
        $expert = Expert::where('user_id', $request->user()->id_user)->first();

        if (! $expert) {
            return $this->successResponse([], 'Profil pakar belum tersedia.');
        }

        $queue = Consultation::query()
            ->with(['user:id_user,full_name,username,image,avatar_url', 'expert.user'])
            ->where('expert_id', $expert->id)
            ->whereIn('status', ['pending', 'paid', 'active'])
            ->latest()
            ->get()
            ->map(fn (Consultation $consultation) => $this->consultationPayload($consultation, true));

        return $this->successResponse($queue, 'Antrean pakar berhasil diambil.');
    }

    private function consultationPayload(Consultation $consultation, bool $includeUser = false): array
    {
        $data = [
            'id' => $consultation->id,
            'status' => $consultation->status,
            'payment_token' => $consultation->payment_token,
            'payment_status' => $consultation->payment_status,
            'amount' => (int) $consultation->amount,
            'started_at' => optional($consultation->started_at)->toISOString(),
            'ended_at' => optional($consultation->ended_at)->toISOString(),
            'created_at' => optional($consultation->created_at)->toISOString(),
            'expert' => $consultation->expert ? [
                'id' => $consultation->expert->id,
                'user_id' => $consultation->expert->user_id,
                'name' => $consultation->expert->user?->full_name ?? $consultation->expert->user?->username,
                'photo_url' => $consultation->expert->user?->avatar_url ?? $consultation->expert->user?->image,
                'specialization' => $consultation->expert->specialization,
                'bio' => $consultation->expert->bio,
                'is_verified' => (bool) $consultation->expert->is_verified,
                'is_online' => (bool) $consultation->expert->is_online,
                'price_per_session' => (int) $consultation->expert->price_per_session,
                'rating' => (float) $consultation->expert->rating,
                'total_reviews' => (int) $consultation->expert->total_reviews,
            ] : null,
        ];

        if ($includeUser) {
            $data['user'] = [
                'id' => $consultation->user?->id_user,
                'name' => $consultation->user?->full_name ?? $consultation->user?->username,
                'photo_url' => $consultation->user?->avatar_url ?? $consultation->user?->image,
            ];
        }

        return $data;
    }
}
