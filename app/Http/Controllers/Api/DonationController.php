<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtransService
    ) {
    }

    public function campaigns(): JsonResponse
    {
        $campaigns = DonationCampaign::query()
            ->where('is_active', true)
            ->orderByDesc('is_urgent')
            ->orderBy('deadline')
            ->get()
            ->map(fn (DonationCampaign $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'slug' => $c->slug,
                'description' => $c->description,
                'image' => $c->image,
                'target_amount' => (float) $c->target_amount,
                'collected_amount' => (float) $c->collected_amount,
                'donor_count' => $c->donor_count,
                'category' => $c->category,
                'is_urgent' => $c->is_urgent,
                'deadline' => $c->deadline?->toIso8601String(),
                'progress_percent' => $c->progressPercent(),
            ]);

        return response()->json(['success' => true, 'data' => $campaigns]);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:donation_campaigns,id',
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'nullable|string|max:50',
            'is_anonymous' => 'boolean',
            'donor_name' => 'nullable|string|max:200',
            'donor_message' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $campaign = DonationCampaign::findOrFail($validated['campaign_id']);
        $orderId = 'DON-' . $campaign->id . '-' . now()->timestamp . '-' . Str::upper(Str::random(4));

        $donation = Donation::create([
            'user_id' => $user->id_user ?? $user->id ?? null,
            'campaign_id' => $campaign->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? 'midtrans',
            'transaction_id' => $orderId,
            'payment_status' => 'pending',
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
            'donor_name' => $validated['donor_name'] ?? ($user->full_name ?? $user->username ?? 'Donatur'),
            'donor_message' => $validated['donor_message'] ?? null,
            'expired_at' => now()->addHours(24),
        ]);

        $midtrans = $this->midtransService->createTransaction([
            'order_id' => $orderId,
            'gross_amount' => (int) round((float) $validated['amount']),
            'item_name' => 'Donasi: ' . Str::limit($campaign->title, 40),
            'customer_details' => [
                'first_name' => $donation->donor_name ?? 'Donatur',
                'email' => $user->email ?? 'donor@halalytics.local',
                'phone' => $user->phone ?? '',
            ],
        ]);

        $donation->update([
            'midtrans_token' => $midtrans['token'] ?? null,
            'payment_url' => $midtrans['redirect_url'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'donation_id' => $donation->id,
                'transaction_id' => $orderId,
                'snap_token' => $midtrans['token'] ?? null,
                'payment_url' => $midtrans['redirect_url'] ?? null,
                'is_mock' => $midtrans['is_mock'] ?? false,
            ],
        ]);
    }

    public function history(): JsonResponse
    {
        $userId = Auth::user()->id_user ?? Auth::id();

        $donations = Donation::query()
            ->with('campaign:id,title,slug,image')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn (Donation $d) => [
                'id' => $d->id,
                'amount' => (float) $d->amount,
                'payment_status' => $d->payment_status,
                'campaign' => $d->campaign,
                'paid_at' => $d->paid_at?->toIso8601String(),
                'created_at' => $d->created_at?->toIso8601String(),
            ]);

        return response()->json(['success' => true, 'data' => $donations]);
    }
}
