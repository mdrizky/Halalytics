<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use App\Models\ProductModel;
use App\Models\ReportModel;
use App\Models\ScanModel;
use App\Models\BpomData;
use App\Services\UniversalProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPortalController extends Controller
{
    protected UniversalProductService $universalProductService;

    public function __construct(UniversalProductService $universalProductService)
    {
        $this->universalProductService = $universalProductService;
    }

    private function currentUserId(): ?int
    {
        return Auth::user()?->id_user;
    }

    public function dashboard()
    {
        $userId = $this->currentUserId();

        $stats = [
            'scan_count' => ScanModel::where('user_id', $userId)->count(),
            'report_count' => ReportModel::where('user_id', $userId)->count(),
            'catalog_count' => ProductModel::where('active', true)->count(),
        ];

        $featuredProducts = ProductModel::with('kategori')
            ->where('active', true)
            ->latest('id_product')
            ->limit(4)
            ->get();

        $recentScans = ScanModel::with('product.kategori')
            ->where('user_id', $userId)
            ->latest('tanggal_scan')
            ->limit(5)
            ->get();
            
        return view('user.dashboard', compact('stats', 'recentScans', 'featuredProducts'));
    }

    public function myScans()
    {
        $scans = ScanModel::with('product.kategori')
            ->where('user_id', $this->currentUserId())
            ->orderByDesc('tanggal_scan')
            ->paginate(10);

        return view('user.my_scans', compact('scans'));
    }

    public function compose(Request $request)
    {
        $products = $this->buildCatalogQuery($request)->paginate(8)->withQueryString();
        $categories = KategoriModel::orderBy('nama_kategori')->get();
        $cartItems = $this->cartItems();
        $cartSummary = $this->cartSummary($cartItems);

        return view('user.compose', compact('products', 'categories', 'cartItems', 'cartSummary'));
    }

    public function products(Request $request)
    {
        $products = $this->buildCatalogQuery($request)->paginate(12)->withQueryString();
        $categories = KategoriModel::orderBy('nama_kategori')->get();

        return view('user.products', compact('products', 'categories'));
    }

    public function productDetail(ProductModel $product)
    {
        $product->load('kategori');

        abort_unless($product->active, 404);

        $relatedProducts = ProductModel::with('kategori')
            ->where('active', true)
            ->where('id_product', '!=', $product->id_product)
            ->when($product->kategori_id, fn ($query) => $query->where('kategori_id', $product->kategori_id))
            ->latest('id_product')
            ->limit(4)
            ->get();

        return view('user.product_detail', compact('product', 'relatedProducts'));
    }

    public function productDetailByBarcode(string $barcode)
    {
        $result = $this->universalProductService->findProduct($barcode);

        abort_unless($result['found'] ?? false, 404, 'Produk tidak ditemukan.');

        $product = $result['data'];
        if ($product instanceof ProductModel) {
            $product->load('kategori');
        }

        $relatedProducts = ProductModel::with('kategori')
            ->where('active', true)
            ->when($product instanceof ProductModel, fn ($query) => $query->where('id_product', '!=', $product->id_product))
            ->when($product instanceof BpomData && $product->kategori, function ($query) use ($product) {
                return $query->whereHas('kategori', fn ($q) => $q->where('nama_kategori', 'like', "%{$product->kategori}%"));
            }, function ($query) use ($product) {
                if ($product instanceof ProductModel) {
                    return $query->when($product->kategori_id, fn ($q) => $q->where('kategori_id', $product->kategori_id));
                }

                return $query;
            })
            ->latest('id_product')
            ->limit(4)
            ->get();

        return view('user.product_detail', compact('product', 'relatedProducts'));
    }

    public function reports()
    {
        $reports = ReportModel::with('product.kategori')
            ->where('user_id', $this->currentUserId())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.reports', compact('reports'));
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'laporan' => 'required|string',
            'product_id' => 'nullable|exists:products,id_product',
            'reason' => 'nullable|string|max:100',
        ]);

        $productId = $request->product_id;
        if (! $productId) {
            $product = ProductModel::where('nama_product', $request->product_name)->first()
                ?: ProductModel::where('nama_product', 'like', '%' . $request->product_name . '%')->first();

            if (! $product) {
                $product = ProductModel::create([
                    'nama_product' => $request->product_name,
                    'status' => 'unknown',
                    'active' => false,
                    'source' => 'user_report',
                    'verification_status' => 'pending',
                    'price' => null,
                ]);
            }

            $productId = $product->id_product;
        }

        ReportModel::create([
            'user_id' => $this->currentUserId(),
            'product_id' => $productId,
            'reason' => $request->input('reason', 'other'),
            'laporan' => $request->laporan,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Laporan Anda telah berhasil dikirim dan akan segera ditinjau oleh tim kami.');
    }

    public function scanner()
    {
        return view('user.scanner');
    }

    private function buildCatalogQuery(Request $request)
    {
        return ProductModel::with('kategori')
            ->where('active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('nama_product', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('kategori_id', $request->category);
            })
            ->latest('id_product');
    }
}
