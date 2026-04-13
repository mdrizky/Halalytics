<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\HalalProduct;
use App\Models\ActivityModel;
use App\Services\DisplayImageService;
use App\Services\HalalCertificationService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $halalService;
    protected $universalProductService;
    protected $displayImageService;

    public function __construct(
        HalalCertificationService $halalService,
        \App\Services\UniversalProductService $universalProductService,
        DisplayImageService $displayImageService
    ) {
        $this->halalService = $halalService;
        $this->universalProductService = $universalProductService;
        $this->displayImageService = $displayImageService;
    }

    /**
     * Get product details from universal sources (Local -> OFF -> OBF)
     */
    public function show($barcode)
    {
        try {
            $result = $this->universalProductService->findProduct($barcode);

            if ($result['found']) {
                $productData = $result['standardized'] ?? [];
                $normalizedProduct = $this->normalizeProductPayload($productData, $result['data'] ?? null);
                $halalInfo = [
                    'halal_status' => data_get($productData, 'status_halal', 'unknown'),
                    'halal_certificate_number' => data_get($productData, 'halal_certificate'),
                    'certification_body' => data_get($productData, 'certification_body'),
                    'source' => $result['source'] ?? 'unknown',
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Detail produk berhasil dimuat',
                    'data' => [
                        'product' => $normalizedProduct,
                        'halal_info' => $halalInfo,
                        'halal_source' => $result['source'] ?? 'unknown',
                    ],
                ]);
            }
        } catch (\Throwable $throwable) {
            Log::warning('ProductController show failed', [
                'barcode' => $barcode,
                'error' => $throwable->getMessage(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk belum ditemukan. Coba scan ulang atau cek barcode produk.',
            'data' => [
                'product' => $this->normalizeProductPayload([
                    'barcode' => $barcode,
                    'name' => 'Produk belum ditemukan',
                    'brand' => 'Merek tidak tersedia',
                    'ingredients_text' => 'Komposisi belum tersedia',
                    'category' => 'Produk Umum',
                    'status_halal' => 'unknown',
                ]),
                'halal_info' => [
                    'halal_status' => 'unknown',
                    'halal_certificate_number' => null,
                    'certification_body' => null,
                    'source' => 'fallback',
                ],
                'halal_source' => 'fallback',
            ],
        ], 404);
    }

    /**
     * Check halal status only
     */
    public function checkHalal(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'product_name' => 'required|string',
            'brand' => 'nullable|string'
        ]);

        $result = $this->halalService->verifyAndStore(
            $request->barcode,
            $request->product_name,
            $request->brand
        );

        // Record Activity
        if (auth('sanctum')->check()) {
            ActivityModel::create([
                'id_user' => auth('sanctum')->id(),
                'aktivitas' => "Mengecek status halal: " . $request->product_name,
                'status' => $result['data']->halal_status
            ]);
        }

        return response()->json([
            'success' => true,
            'halal_status' => $result['data']->halal_status,
            'certificate_number' => $result['data']->halal_certificate_number,
            'certification_body' => $result['data']->certification_body,
            'valid_until' => $result['data']->certificate_valid_until,
            'last_checked' => $result['data']->last_checked_at,
            'source' => $result['source']
        ]);
    }

    /**
     * Batch check multiple products
     */
    public function batchCheckHalal(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.barcode' => 'required|string',
            'products.*.name' => 'required|string',
            'products.*.brand' => 'nullable|string'
        ]);

        $results = [];

        foreach ($request->products as $product) {
            $result = $this->halalService->verifyAndStore(
                $product['barcode'],
                $product['name'],
                $product['brand'] ?? ''
            );

            $results[] = [
                'barcode' => $product['barcode'],
                'halal_status' => $result['data']->halal_status,
                'certificate_number' => $result['data']->halal_certificate_number
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get halal alternatives for a consumer product using AI
     */
    public function alternatives(Request $request, $barcode)
    {
        try {
            // First, find the product so we know its name and ingredients
            $result = $this->universalProductService->findProduct($barcode);
            
            if (!$result['found']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan, tidak bisa mencari alternatif.'
                ], 404);
            }

            $productData = $result['standardized'];
            $productName = $productData['name'];
            $ingredients = $productData['ingredients_text'];
            $category = $productData['category'] ?? 'makanan/minuman umum';

            // Invoke Gemini AI to find alternatives
            $geminiService = app(GeminiService::class);
            $aiResponse = $geminiService->findProductHalalAlternative($productName, $ingredients, $category);

            // Record Activity
            if (auth('sanctum')->check()) {
                ActivityModel::create([
                    'id_user' => auth('sanctum')->id(),
                    'aktivitas' => "Mencari alternatif halal untuk: " . $productName,
                    'status' => 'success'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $aiResponse
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Halal Product Alternative Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari alternatif produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular products (most scanned)
     */
    public function popular(Request $request)
    {
        $products = \App\Models\ScanHistory::select('product_name', 'barcode', 'status')
            ->selectRaw('COUNT(*) as scan_count')
            ->whereNotNull('product_name')
            ->groupBy('product_name', 'barcode', 'status')
            ->orderByDesc('scan_count')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'barcode' => $item->barcode,
                    'halal_status' => $item->status ?? 'unknown',
                    'scan_count' => $item->scan_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get recently added products
     */
    public function recent()
    {
        $products = ProductModel::orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id_product,
                    'name' => $p->nama_product ?? $p->name ?? 'Produk tanpa nama',
                    'brand' => $p->brand ?: 'Merek belum tersedia',
                    'barcode' => $p->barcode,
                    'halal_status' => $p->halal_status ?? 'unknown',
                    'image_url' => $this->displayImageService->resolve($p->image, [
                        'name' => $p->nama_product ?? $p->name,
                        'brand' => $p->brand,
                        'barcode' => $p->barcode,
                        'category' => $p->kategori ?? $p->category ?? 'product',
                    ], 'product'),
                    'created_at' => $p->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get user's scan history
     */
    public function scanHistory(Request $request)
    {
        $user = $request->user();
        $scans = \App\Models\ScanHistory::where('user_id', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $scans,
        ]);
    }

    /**
     * Get user's favorite products
     */
    public function favorites(Request $request)
    {
        $user = $request->user();
        $favorites = \App\Models\FavoriteProduct::where('user_id', $user->id_user)
            ->with(['ocrProduct', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $favorites,
        ]);
    }

    private function normalizeProductPayload(array $productData, $model = null): array
    {
        $productId = 0;
        if ($model instanceof \App\Models\BpomData) {
            $productId = $model->id;
        } elseif ($model instanceof \App\Models\ProductModel) {
            $productId = $model->id_product;
        }

        $resolvedImage = $this->displayImageService->resolve(
            data_get($productData, 'image_url'),
            [
                'name' => data_get($productData, 'name'),
                'brand' => data_get($productData, 'brand'),
                'barcode' => data_get($productData, 'barcode'),
                'category' => data_get($productData, 'category', 'product'),
            ],
            'product'
        );

        return [
            'id' => $productId,
            'barcode' => data_get($productData, 'barcode'),
            'name' => data_get($productData, 'name', 'Produk tanpa nama'),
            'brand' => data_get($productData, 'brand', 'Merek belum tersedia'),
            'image_front_url' => $resolvedImage,
            'image' => $resolvedImage,
            'ingredients_text' => data_get($productData, 'ingredients_text', 'Komposisi belum tersedia'),
            'category' => data_get($productData, 'category', 'Produk Umum'),
            'nutriscore' => data_get($productData, 'nutriscore'),
            'additives' => data_get($productData, 'additives', []),
            'allergens' => data_get($productData, 'allergens', []),
        ];
    }
}
