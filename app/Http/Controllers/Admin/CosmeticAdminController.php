<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use App\Services\CosmeticExternalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CosmeticAdminController extends Controller
{
    protected $cosmeticExternalService;

    public function __construct(CosmeticExternalService $cosmeticExternalService)
    {
        $this->cosmeticExternalService = $cosmeticExternalService;
    }

    public function index(Request $request)
    {
        if (
            !$request->filled('search') &&
            !$request->filled('status') &&
            BpomData::where('kategori', 'kosmetik')->count() === 0
        ) {
            $this->seedLocalFallbackCosmetics();
        }

        $query = BpomData::where('kategori', 'kosmetik');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_reg', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_keamanan', $request->status);
        }

        if ($request->filled('source')) {
            if ($request->source === 'lokal') {
                $query->whereNotIn('sumber_data', ['open_beauty_facts', 'open_beauty_facts_api']);
            } else {
                $query->where('sumber_data', $request->source);
            }
        }

        $cosmetics = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => BpomData::where('kategori', 'kosmetik')->count(),
            'aman' => BpomData::where('kategori', 'kosmetik')->where('status_keamanan', 'aman')->count(),
            'waspada' => BpomData::where('kategori', 'kosmetik')->where('status_keamanan', 'waspada')->count(),
            'bahaya' => BpomData::where('kategori', 'kosmetik')->where('status_keamanan', 'bahaya')->count(),
            'haram' => BpomData::where('kategori', 'kosmetik')
                ->where(function ($q) {
                    $q->where('status_halal', 'haram')
                        ->orWhere('status_keamanan', 'haram');
                })
                ->count(),
            'from_obf' => BpomData::where('kategori', 'kosmetik')
                ->whereIn('sumber_data', ['open_beauty_facts', 'open_beauty_facts_api'])
                ->count(),
        ];

        return view('admin.cosmetic.index', compact('cosmetics', 'stats'));
    }

    private function seedLocalFallbackCosmetics(): void
    {
        $items = [
            ['nama_produk' => 'Hydrating Face Wash', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
            ['nama_produk' => 'Daily Sunscreen SPF 50', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
            ['nama_produk' => 'Vitamin C Serum', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'waspada'],
            ['nama_produk' => 'Niacinamide Bright Toner', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
            ['nama_produk' => 'Gentle Cleansing Balm', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
            ['nama_produk' => 'Whitening Body Lotion', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'waspada'],
            ['nama_produk' => 'Long Lasting Lip Tint', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
            ['nama_produk' => 'Hair Vitamin Repair', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
        ];

        foreach ($items as $item) {
            BpomData::firstOrCreate(
                ['nama_produk' => $item['nama_produk'], 'kategori' => 'kosmetik'],
                [
                    'merk' => $item['merk'],
                    'status_keamanan' => $item['status_keamanan'],
                    'status_halal' => 'belum_diverifikasi',
                    'verification_status' => 'pending',
                    'sumber_data' => 'fallback_seed',
                ]
            );
        }
    }

    public function searchExternal(Request $request)
    {
        $request->validate(['query' => 'required|string|min:2']);

        $query = $request->input('query');
        $results = [];

        try {
            $results = $this->cosmeticExternalService->search($query);
        } catch (\Exception $e) {
            Log::error('OpenBeautyFacts search error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'query' => $query
        ]);
    }

    public function importExternal(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'brand' => 'nullable|string',
            'barcode' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $this->cosmeticExternalService->importOrUpdate([
            'name' => $request->name,
            'brand' => $request->brand,
            'barcode' => $request->barcode,
            'ingredients' => $request->ingredients,
            'image_url' => $request->image_url,
        ]);

        return back()->with('success', "Kosmetik '{$request->name}' berhasil diimport!");
    }

    /**
     * Seed common cosmetics from Open Beauty Facts
     */
    public function seedCosmetics()
    {
        $searchTerms = ['sunscreen', 'moisturizer', 'cleanser', 'serum', 'lipstick', 'shampoo', 'body lotion', 'face cream', 'toner', 'foundation'];
        $imported = 0;

        foreach ($searchTerms as $term) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'Halalytics/1.0 (contact@halalytics.id)'])
                    ->get("https://world.openbeautyfacts.org/cgi/search.pl", [
                        'search_terms' => $term,
                        'search_simple' => 1,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => 5,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    foreach (($data['products'] ?? []) as $p) {
                        $name = $p['product_name'] ?? $p['product_name_en'] ?? null;
                        if (!$name) continue;

                        $barcode = $p['code'] ?? null;
                        if ($barcode && BpomData::where('barcode', $barcode)->exists()) continue;
                        if (BpomData::where('nama_produk', $name)->where('kategori', 'kosmetik')->exists()) continue;

                        BpomData::create([
                            'nama_produk' => $name,
                            'merk' => $p['brands'] ?? null,
                            'barcode' => $barcode,
                            'kategori' => 'kosmetik',
                            'ingredients_text' => $p['ingredients_text'] ?? $p['ingredients_text_en'] ?? null,
                            'image_url' => $p['image_url'] ?? $p['image_front_url'] ?? null,
                            'status_keamanan' => 'aman',
                            'sumber_data' => 'open_beauty_facts',
                            'verification_status' => 'pending',
                        ]);
                        $imported++;
                    }
                }

                usleep(500000); // 500ms delay for rate limiting
            } catch (\Exception $e) {
                Log::error("Seed cosmetics error for {$term}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Berhasil import {$imported} produk kosmetik dari OpenBeautyFacts!");
    }
}
