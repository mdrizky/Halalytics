<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationCampaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $campaigns = DonationCampaign::withCount('donations')
            ->orderByDesc('created_at')
            ->paginate(15);
            
        $stats = [
            'total' => DonationCampaign::count(),
            'active' => DonationCampaign::where('is_active', true)->count(),
            'total_donations' => Donation::where('payment_status', 'settlement')->sum('amount'),
            'total_donors' => Donation::where('payment_status', 'settlement')->distinct('user_id')->count('user_id'),
        ];

        return view('admin.donation_campaigns.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        return view('admin.donation_campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1000',
            'category' => 'required|string|max:100',
            'deadline' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
            'is_urgent' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/donations');
            $validated['image'] = asset(str_replace('public/', 'storage/', $path));
        } else {
            // Apply default banners based on category if image is missing but template is used
            $categoryImages = [
                'palestina' => 'https://images.unsplash.com/photo-1595152452543-e5fc28ebc2b8?w=800',
                'masjid' => 'https://images.unsplash.com/photo-1595152452543-e5fc28ebc2b8?w=800',
                'bencana' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=800',
                'yatim' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'
            ];
            // Since we don't have the template key in $request, we can guess based on title if needed
            // But usually admin will upload. For now, we'll keep it as is.
        }

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_urgent'] = $request->boolean('is_urgent', false);
        $validated['collected_amount'] = 0;
        $validated['donor_count'] = 0;

        DonationCampaign::create($validated);

        return redirect()->route('admin.donation-campaigns.index')
            ->with('success', 'Kampanye donasi berhasil dibuat.');
    }

    public function show(DonationCampaign $donationCampaign)
    {
        $donations = $donationCampaign->donations()
            ->with('user:id_user,full_name,username,email')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.donation_campaigns.show', compact('donationCampaign', 'donations'));
    }

    public function edit(DonationCampaign $donationCampaign)
    {
        return view('admin.donation_campaigns.edit', compact('donationCampaign'));
    }

    public function update(Request $request, DonationCampaign $donationCampaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1000',
            'category' => 'required|string|max:100',
            'deadline' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
            'is_urgent' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($donationCampaign->image && str_contains($donationCampaign->image, '/storage/donations/')) {
                $oldPath = str_replace(asset('storage/'), 'public/', $donationCampaign->image);
                Storage::delete($oldPath);
            }
            $path = $request->file('image')->store('public/donations');
            $validated['image'] = asset(str_replace('public/', 'storage/', $path));
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_urgent'] = $request->boolean('is_urgent');

        if ($validated['title'] !== $donationCampaign->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }

        $donationCampaign->update($validated);

        return redirect()->route('admin.donation-campaigns.index')
            ->with('success', 'Kampanye donasi berhasil diperbarui.');
    }

    public function destroy(DonationCampaign $donationCampaign)
    {
        if ($donationCampaign->donations()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kampanye yang sudah memiliki donatur.');
        }

        if ($donationCampaign->image && str_contains($donationCampaign->image, '/storage/donations/')) {
            $oldPath = str_replace(asset('storage/'), 'public/', $donationCampaign->image);
            Storage::delete($oldPath);
        }

        $donationCampaign->delete();

        return redirect()->route('admin.donation-campaigns.index')
            ->with('success', 'Kampanye donasi berhasil dihapus.');
    }
}
