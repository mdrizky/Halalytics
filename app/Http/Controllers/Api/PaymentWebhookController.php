<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtransService
    ) {
    }

    /**
     * Midtrans payment notification (donations + consultations).
     */
    public function midtrans(Request $request)
    {
        $payload = $request->all();

        if (! $this->midtransService->isValidSignature($payload)) {
            Log::warning('Midtrans webhook invalid signature', ['order_id' => $payload['order_id'] ?? null]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $status = strtolower((string) ($payload['transaction_status'] ?? ''));
        $fraud = strtolower((string) ($payload['fraud_status'] ?? 'accept'));

        if (str_starts_with($orderId, 'DON-')) {
            $this->handleDonation($orderId, $status, $fraud);
        }

        return response()->json(['message' => 'OK']);
    }

    private function handleDonation(string $orderId, string $status, string $fraud): void
    {
        $donation = Donation::query()->where('transaction_id', $orderId)->first();
        if (! $donation) {
            return;
        }

        $paid = in_array($status, ['capture', 'settlement'], true)
            && ($fraud === 'accept' || $fraud === '');

        if ($paid && $donation->payment_status !== 'paid') {
            $donation->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            $campaign = DonationCampaign::find($donation->campaign_id);
            if ($campaign) {
                $campaign->increment('collected_amount', $donation->amount);
                $campaign->increment('donor_count');
            }
        } elseif (in_array($status, ['deny', 'cancel', 'expire'], true)) {
            $donation->update(['payment_status' => $status === 'expire' ? 'expired' : 'failed']);
        }
    }
}
