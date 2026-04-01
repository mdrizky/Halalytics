<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use App\Services\BpomService;
use Illuminate\Http\Request;

class BpomAdminController extends Controller
{
    protected $bpomService;

    public function __construct(BpomService $bpomService)
    {
        $this->bpomService = $bpomService;
    }

    public function index(Request $request)
    {
        if (
            !$request->filled('search') &&
            !$request->filled('kategori') &&
            !$request->filled('status') &&
            BpomData::count() === 0
        ) {
            $this->seedLocalFallbackBpom();
        }

        $query = BpomData::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_reg', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status_keamanan', $request->status);
        }

        $bpom_data = $query->latest()->paginate(20)->withQueryString();
        
        $stats = [
            'total' => BpomData::count(),
            'verified' => BpomData::where('verification_status', 'verified')->count(),
            'ai_generated' => BpomData::where('sumber_data', 'ai')->count(),
            'dangerous' => BpomData::where('status_keamanan', 'bahaya')->count(),
        ];

        return view('admin.bpom.index', compact('bpom_data', 'stats'));
    }

    private function seedLocalFallbackBpom(): void
    {
        $items = [
            ['nomor_reg' => 'NA18240100001', 'kategori' => 'kosmetik', 'nama_produk' => 'Fallback Skin Moisturizer', 'merk' => 'Fallback Beauty', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'NA18240100003', 'kategori' => 'kosmetik', 'nama_produk' => 'Hydrating Sunscreen SPF50', 'merk' => 'SkinLabs', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'NA18240100004', 'kategori' => 'kosmetik', 'nama_produk' => 'Night Repair Serum', 'merk' => 'DermaEssence', 'status_keamanan' => 'waspada'],
            ['nomor_reg' => 'NA18240100005', 'kategori' => 'kosmetik', 'nama_produk' => 'Matte Lip Cream', 'merk' => 'BeautyPop', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'MD22450100002', 'kategori' => 'pangan', 'nama_produk' => 'Fallback Chicken Nugget', 'merk' => 'Fallback Food', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'MD22450100006', 'kategori' => 'pangan', 'nama_produk' => 'Instant Oat Drink', 'merk' => 'NutriMeal', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'MD22450100007', 'kategori' => 'pangan', 'nama_produk' => 'Chocolate Wafer', 'merk' => 'SnackHub', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'MD22450100008', 'kategori' => 'pangan', 'nama_produk' => 'Creamy Peanut Spread', 'merk' => 'DailySpread', 'status_keamanan' => 'waspada'],
            ['nomor_reg' => 'TR21240100009', 'kategori' => 'obat', 'nama_produk' => 'Paracetamol 500', 'merk' => 'MediCare', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'TR21240100010', 'kategori' => 'obat', 'nama_produk' => 'Ibuprofen 200', 'merk' => 'MediCare', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'SI22450100011', 'kategori' => 'suplemen', 'nama_produk' => 'Vitamin C 1000', 'merk' => 'HealthOne', 'status_keamanan' => 'aman'],
            ['nomor_reg' => 'SI22450100012', 'kategori' => 'suplemen', 'nama_produk' => 'Collagen Plus', 'merk' => 'HealthOne', 'status_keamanan' => 'waspada'],
        ];

        foreach ($items as $item) {
            BpomData::firstOrCreate(
                ['nomor_reg' => $item['nomor_reg']],
                [
                    'kategori' => $item['kategori'],
                    'nama_produk' => $item['nama_produk'],
                    'merk' => $item['merk'],
                    'status_keamanan' => $item['status_keamanan'],
                    'status_halal' => 'belum_diverifikasi',
                    'verification_status' => 'pending',
                    'sumber_data' => 'fallback_seed',
                ]
            );
        }
    }

    public function show($id)
    {
        $product = BpomData::findOrFail($id);
        return view('admin.bpom.show', compact('product'));
    }

    public function verify($id)
    {
        $product = BpomData::findOrFail($id);
        $product->update([
            'verification_status' => 'verified',
            'sumber_data' => 'bpom_resmi',
            'verified_at' => now()
        ]);

        return back()->with('success', 'Produk berhasil diverifikasi.');
    }

    public function destroy($id)
    {
        $product = BpomData::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Data BPOM berhasil dihapus.');
    }

    /**
     * Sync data from external APIs (OpenFoodFacts halal products + OpenBeautyFacts cosmetics)
     */
    public function syncExternal()
    {
        $result = $this->bpomService->syncLatest(100);

        $message = sprintf(
            'Sync BPOM selesai. %d data diproses.',
            (int) ($result['synced_count'] ?? 0)
        );

        if (!empty($result['errors'])) {
            $message .= ' Error: ' . implode('; ', $result['errors']);
        }

        return back()->with('success', $message);
    }
}
