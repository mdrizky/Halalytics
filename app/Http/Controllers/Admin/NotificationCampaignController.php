<?php

namespace App\Http\Controllers\Admin;

use App\Models\NotificationCampaign;
use App\Models\ScanHistory;
use App\Models\UserFcmToken;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NotificationCampaignController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->middleware(['auth', 'admin']);
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $campaigns = NotificationCampaign::with('creator')
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total' => NotificationCampaign::count(),
            'draft' => NotificationCampaign::where('status', 'draft')->count(),
            'sent' => NotificationCampaign::where('status', 'sent')->count(),
            'scheduled' => NotificationCampaign::where('status', 'scheduled')->count(),
        ];

        return view('admin.campaigns.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        $templates = [
            'promo_ramadan' => [
                'name' => 'Promo Ramadan',
                'title' => 'Spesial Ramadan',
                'body' => 'Cek produk halal pilihanmu selama bulan suci!',
            ],
            'new_products' => [
                'name' => 'Produk Baru',
                'title' => 'Produk Baru Tersedia!',
                'body' => 'Lihat produk halal baru yang baru ditambahkan minggu ini.',
            ],
            'daily_reminder' => [
                'name' => 'Scan Harian',
                'title' => 'Jangan Lupa Scan!',
                'body' => 'Jaga streak scan harianmu — scan minimal 1 produk hari ini!',
            ],
            'health_article' => [
                'name' => 'Artikel Baru',
                'title' => 'Artikel Kesehatan Baru',
                'body' => 'Baca artikel terbaru tentang kesehatan halal.',
            ],
        ];

        return view('admin.campaigns.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'image_url' => 'nullable|url|max:500',
            'action_url' => 'nullable|string|max:500',
            'target_mode' => 'required|in:all,specific_users',
            'user_ids' => 'nullable|string|max:2000',
            'active_only' => 'nullable|boolean',
            'data_type' => 'nullable|string|max:50',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $targetSegment = $this->buildTargetSegment($request, $validated);

        $validated['created_by'] = Auth::user()->id_user ?? Auth::id();
        $validated['status'] = $request->has('scheduled_at') ? 'scheduled' : 'draft';
        $validated['target_segment'] = $targetSegment;

        $campaign = NotificationCampaign::create($validated);

        if ($request->boolean('send_now')) {
            $result = $this->sendCampaign($campaign);

            return redirect()->route('admin.campaigns.show', $campaign)
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        return redirect()->route('admin.campaigns.show', $campaign)
            ->with('success', "Kampanye '{$campaign->name}' berhasil dibuat.");
    }

    public function show(NotificationCampaign $campaign)
    {
        $segment = $campaign->target_segment ?? [];
        $estimatedTargetCount = $this->buildTokenQuery($segment)
            ->distinct('fcm_token')
            ->count('fcm_token');

        return view('admin.campaigns.show', compact('campaign', 'estimatedTargetCount'));
    }

    public function edit(NotificationCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return back()->with('error', 'Kampanye yang sudah dikirim tidak bisa diedit.');
        }

        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, NotificationCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return back()->with('error', 'Kampanye yang sudah dikirim tidak bisa diubah.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'image_url' => 'nullable|url|max:500',
            'action_url' => 'nullable|string|max:500',
            'target_mode' => 'required|in:all,specific_users',
            'user_ids' => 'nullable|string|max:2000',
            'active_only' => 'nullable|boolean',
            'data_type' => 'nullable|string|max:50',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['target_segment'] = $this->buildTargetSegment($request, $validated);
        $campaign->update($validated);

        return redirect()->route('admin.campaigns.show', $campaign)
            ->with('success', "Kampanye '{$campaign->name}' berhasil diperbarui.");
    }

    public function destroy(NotificationCampaign $campaign)
    {
        if ($campaign->status === 'sending') {
            return back()->with('error', 'Kampanye yang sedang dikirim tidak bisa dihapus.');
        }

        $campaign->delete();
        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye berhasil dihapus.');
    }

    /**
     * Send a campaign
     */
    public function send(NotificationCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return back()->with('error', 'Kampanye sudah pernah dikirim.');
        }

        $result = $this->sendCampaign($campaign);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function sendCampaign(NotificationCampaign $campaign)
    {
        $campaign->update(['status' => 'sending']);

        try {
            $segment = $campaign->target_segment ?? [];
            $tokens = $this->buildTokenQuery($segment)
                ->pluck('fcm_token')
                ->filter()
                ->unique()
                ->values()
                ->all();
            $targetCount = count($tokens);

            if ($targetCount === 0) {
                $campaign->update([
                    'status' => 'failed',
                    'target_count' => 0,
                    'sent_count' => 0,
                ]);

                return [
                    'success' => false,
                    'message' => "Kampanye '{$campaign->name}' gagal dikirim karena belum ada token FCM yang cocok.",
                ];
            }

            $payload = array_filter([
                'type' => $segment['data_type'] ?? 'general',
                'campaign_id' => (string) $campaign->id,
                'action_url' => $campaign->action_url,
                'image_url' => $campaign->image_url,
            ], function ($value) {
                return $value !== null && $value !== '';
            });

            $result = $this->firebaseService->sendToTokens(
                $tokens,
                $campaign->title,
                $campaign->body,
                $payload,
                $campaign->image_url
            );

            Log::info("Sending campaign '{$campaign->name}' to {$targetCount} devices", [
                'campaign_id' => $campaign->id,
                'title' => $campaign->title,
                'success_count' => $result['success_count'] ?? 0,
                'failure_count' => $result['failure_count'] ?? 0,
            ]);

            $campaign->update([
                'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
                'target_count' => $targetCount,
                'sent_count' => $result['success_count'] ?? 0,
                'sent_at' => ($result['success'] ?? false) ? now() : null,
            ]);

            return [
                'success' => (bool) ($result['success'] ?? false),
                'message' => ($result['success'] ?? false)
                    ? "Kampanye '{$campaign->name}' berhasil dikirim ke " . ($result['success_count'] ?? 0) . ' perangkat.'
                    : ('Gagal mengirim kampanye: ' . ($result['message'] ?? 'Unknown Firebase error')),
            ];
        } catch (\Exception $e) {
            $campaign->update(['status' => 'failed']);
            Log::error("Campaign send failed: {$e->getMessage()}", [
                'campaign_id' => $campaign->id,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal kirim: ' . $e->getMessage(),
            ];
        }
    }

    private function buildTargetSegment(Request $request, array $validated): array
    {
        $userIds = $this->parseUserIds($request->input('user_ids'));

        if (($validated['target_mode'] ?? 'all') === 'specific_users' && empty($userIds)) {
            throw ValidationException::withMessages([
                'user_ids' => 'Isi minimal satu User ID untuk target pengguna tertentu.',
            ]);
        }

        return array_filter([
            'mode' => $validated['target_mode'],
            'user_ids' => $userIds,
            'active_only' => $request->boolean('active_only'),
            'data_type' => $validated['data_type'] ?? 'general',
        ], function ($value) {
            if (is_array($value)) {
                return !empty($value);
            }

            return $value !== null && $value !== '';
        });
    }

    private function buildTokenQuery(array $segment)
    {
        $query = UserFcmToken::query();

        if (($segment['mode'] ?? 'all') === 'specific_users') {
            $query->whereIn('user_id', $segment['user_ids'] ?? []);
        }

        if (!empty($segment['active_only'])) {
            $activeUserIds = ScanHistory::where('created_at', '>=', now()->subDays(7))
                ->distinct()
                ->pluck('user_id');

            $query->whereIn('user_id', $activeUserIds);
        }

        return $query->orderByDesc('last_used_at');
    }

    private function parseUserIds(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        return collect(preg_split('/[\s,]+/', $raw))
            ->map(function ($value) {
                return (int) trim((string) $value);
            })
            ->filter(function ($value) {
                return $value > 0;
            })
            ->unique()
            ->values()
            ->all();
    }
}
