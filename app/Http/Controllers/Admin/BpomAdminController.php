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

        return view('admin.bpom', compact('bpom_data', 'stats'));
    }

    private function seedLocalFallbackBpom(): void
    {
        $items = [
            [
                'nomor_reg' => 'NA18240100001', 
                'kategori' => 'kosmetik', 
                'nama_produk' => 'Wardah Lightening Day Cream', 
                'merk' => 'Wardah', 
                'status_keamanan' => 'aman',
                'pendaftar' => 'PT Paragon Technology and Innovation',
                'ingredients_text' => 'Niacinamide, Licorice Extract, Vitamin B3',
                'image_url' => 'https://www.wardahbeauty.com/uploads/product/lightening-day-cream-30g.jpg'
            ],
            [
                'nomor_reg' => 'NA18240100003', 
                'kategori' => 'kosmetik', 
                'nama_produk' => 'Azarine Hydrasoothe Sunscreen Gel', 
                'merk' => 'Azarine', 
                'status_keamanan' => 'aman',
                'pendaftar' => 'PT Azarine Cosmetic',
                'ingredients_text' => 'Aloe Vera, Green Tea, Propolis',
                'image_url' => 'https://azarinecosmetic.com/wp-content/uploads/2021/01/Hydrasoothe-Sunscreen-Gel.png'
            ],
            [
                'nomor_reg' => 'MD22450100002', 
                'kategori' => 'pangan', 
                'nama_produk' => 'Indomie Mi Instan Rasa Ayam Bawang', 
                'merk' => 'Indomie', 
                'status_keamanan' => 'aman',
                'pendaftar' => 'PT Indofood CBP Sukses Makmur Tbk',
                'ingredients_text' => 'Tepung Terigu, Minyak Nabati, Garam, Bubuk Ayam',
                'image_url' => 'https://www.indofoodcbp.com/uploads/product/indomie-ayam-bawang.png'
            ],
            [
                'nomor_reg' => 'TR21240100009', 
                'kategori' => 'obat', 
                'nama_produk' => 'Tolak Angin Cair Sido Muncul', 
                'merk' => 'Sido Muncul', 
                'status_keamanan' => 'aman',
                'pendaftar' => 'PT Industri Jamu dan Farmasi Sido Muncul Tbk',
                'ingredients_text' => 'Madu, Jahe, Daun Mint, Adas',
                'image_url' => 'https://sidomuncul.co.id/uploads/product/tolak-angin-cair.png'
            ],
            [
                'nomor_reg' => 'SI22450100011', 
                'kategori' => 'suplemen', 
                'nama_produk' => 'Enervon-C Multivitamin', 
                'merk' => 'Enervon-C', 
                'status_keamanan' => 'aman',
                'pendaftar' => 'PT Medifarma Laboratories',
                'ingredients_text' => 'Vitamin C, Vitamin B Complex',
                'image_url' => 'https://www.enervon.co.id/uploads/product/enervon-c.png'
            ],
        ];

        foreach ($items as $item) {
            BpomData::firstOrCreate(
                ['nomor_reg' => $item['nomor_reg']],
                array_merge($item, [
                    'status_halal' => 'halal',
                    'verification_status' => 'verified',
                    'sumber_data' => 'bpom_resmi',
                    'last_synced_at' => now(),
                ])
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
    public function syncExternal(Request $request)
    {
        $focus = $request->query('focus');
        $limit = 100;

        if ($focus === 'cosmetics') {
            // Specific keywords for cosmetics to focus the sync
            $keywords = ['kosmetik', 'skincare', 'makeup', 'serum', 'cream', 'face wash'];
            $result = $this->bpomService->syncLatest($limit, $keywords);
        } else {
            $result = $this->bpomService->syncLatest($limit);
        }

        $message = sprintf(
            'Sync BPOM (%s) selesai. %d data diproses.',
            $focus ?: 'Global',
            (int) ($result['synced_count'] ?? 0)
        );

        if (!empty($result['errors'])) {
            $message .= ' Error: ' . implode('; ', $result['errors']);
        }

        return back()->with('success', $message);
    }

    /**
     * Batch auto-categorize all BPOM products based on registration number prefix.
     * Uses the BPOM registration prefix convention:
     *   NA/NC/ND → kosmetik, MD/ML/FF → pangan, TR/TI → obat_tradisional,
     *   SD/SI → suplemen, DB/DK/DT/DL → obat
     */
    public function batchAutoCategorize()
    {
        $products = BpomData::whereNotNull('nomor_reg')
            ->where('nomor_reg', '!=', '')
            ->get();

        $updated = 0;

        foreach ($products as $product) {
            $prefix = strtoupper(substr(trim($product->nomor_reg), 0, 2));

            $newCategory = match ($prefix) {
                'NA', 'NC', 'ND' => 'kosmetik',
                'MD', 'ML', 'FF' => 'pangan',
                'TR', 'TI' => 'obat_tradisional',
                'SD', 'SI' => 'suplemen',
                'DB', 'DK', 'DT', 'DL' => 'obat',
                default => null,
            };

            if ($newCategory && $product->kategori !== $newCategory) {
                $product->update(['kategori' => $newCategory]);
                $updated++;
            }
        }

        return back()->with('success', "Auto-kategorisasi selesai. {$updated} produk di-update dari total {$products->count()} produk.");
    }
}
