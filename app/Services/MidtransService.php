<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function createConsultationPayment(Consultation $consultation, User $user, Expert $expert): array
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            return [
                'token' => 'sandbox-token-consultation-' . $consultation->id,
                'redirect_url' => null,
                'order_id' => $this->makeOrderId($consultation),
                'is_mock' => true,
            ];
        }

        $endpoint = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $orderId = $this->makeOrderId($consultation);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $consultation->amount,
            ],
            'customer_details' => [
                'first_name' => $user->full_name ?? $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'item_details' => [[
                'id' => 'consultation-' . $consultation->id,
                'price' => $consultation->amount,
                'quantity' => 1,
                'name' => 'Konsultasi dengan ' . ($expert->user->full_name ?? $expert->user->username ?? 'Pakar'),
            ]],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($endpoint, $payload);

        if (! $response->successful()) {
            Log::warning('Midtrans create transaction failed', [
                'consultation_id' => $consultation->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'token' => null,
                'redirect_url' => null,
                'order_id' => $orderId,
                'error' => $response->json('error_messages.0') ?? 'Gagal membuat transaksi Midtrans.',
            ];
        }

        return [
            'token' => $response->json('token'),
            'redirect_url' => $response->json('redirect_url'),
            'order_id' => $orderId,
        ];
    }

    public function isValidSignature(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            return true;
        }

        $signature = $payload['signature_key'] ?? null;
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;

        if (! $signature || ! $orderId || ! $statusCode || ! $grossAmount) {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signature);
    }

    public function extractConsultationId(?string $orderId): ?int
    {
        if (! $orderId) {
            return null;
        }

        if (preg_match('/^CONSULT-(\d+)-/i', $orderId, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function makeOrderId(Consultation $consultation): string
    {
        return 'CONSULT-' . $consultation->id . '-' . now()->timestamp;
    }
}
