<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Services\ExternalProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid product API: local DB + Open Food/Beauty Facts (+ OpenFDA for barcode).
 */
class ProductController extends Controller
{
    public function __construct(
        protected ExternalProductService $productService
    ) {
    }

    public function detailProduct(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $id = $request->query('id');

        if (!$type || !$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter type and id are required',
            ], 400);
        }

        $data = match ($type) {
            'food' => $this->productService->getFood($id),
            'beauty' => $this->productService->getBeauty($id),
            'drug' => $this->productService->getDrug($id),
            default => null,
        };

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found in external registry',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'category' => $type,
            'result' => $data,
        ]);
    }

    /**
     * GET /api/products/barcode/{barcode}
     * Ensures a local ProductModel row exists for favorites / scan history FK.
     */
    public function show(string $barcode): JsonResponse
    {
        $barcode = trim($barcode);
        if ($barcode === '' || strlen($barcode) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode',
            ], 422);
        }

        $local = ProductModel::query()
            ->where('barcode', $barcode)
            ->first();

        if ($local) {
            return response()->json([
                'success' => true,
                'data' => $this->mapProductModel($local),
            ]);
        }

        $ext = $this->productService->getFood($barcode)
            ?? $this->productService->getBeauty($barcode)
            ?? $this->productService->getDrug($barcode);

        if (!$ext) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product = ProductModel::query()->firstOrCreate(
            ['barcode' => $ext['barcode'] ?? $barcode],
            [
                'nama_product' => $ext['name'] ?? 'Unknown Product',
                'brand' => $ext['brand'] ?? null,
                'status' => $ext['halal_status'] ?? 'unknown',
                'active' => true,
                'source' => $ext['source'] ?? 'external',
                'komposisi' => is_string($ext['ingredients'] ?? null) ? $ext['ingredients'] : null,
                'image' => $ext['image'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $this->mapProductModel($product->fresh()),
        ]);
    }

    /**
     * GET /api/products/search?q=&page=&limit=
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        $q = trim((string) $request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(50, max(1, (int) $request->get('limit', 20)));

        if ($q === '') {
            return response()->json([
                'success' => true,
                'data' => [
                    'products' => [],
                    'total' => 0,
                    'page' => $page,
                    'total_pages' => 0,
                ],
            ]);
        }

        try {
            $food = $this->productService->searchFood($q, $limit, $page);
            $beauty = $this->productService->searchBeauty($q, $limit, $page);
            $merged = array_merge($food['products'] ?? [], $beauty['products'] ?? []);
            $products = [];
            foreach ($merged as $row) {
                $products[] = $this->mapExternalArrayToProductPayload($row);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'total' => count($products),
                    'page' => $page,
                    'total_pages' => max(1, (int) ceil(count($products) / max(1, $limit))),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('ProductController::search failed', ['q' => $q, 'error' => $e->getMessage()]);

            $locals = ProductModel::query()
                ->with('kategori')
                ->where(function ($query) use ($q) {
                    $query->where('nama_product', 'like', "%{$q}%")
                        ->orWhere('barcode', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%");
                })
                ->where(function ($query) {
                    $query->whereNull('active')->orWhere('active', true);
                })
                ->limit($limit)
                ->get()
                ->map(fn (ProductModel $p) => $this->mapProductModel($p))
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $locals,
                    'total' => count($locals),
                    'page' => 1,
                    'total_pages' => 1,
                ],
            ]);
        }
    }

    public function popular(Request $request): JsonResponse
    {
        $limit = min(50, max(1, (int) $request->get('limit', 10)));

        $items = ProductModel::query()
            ->with('kategori')
            ->where(function ($q) {
                $q->whereNull('active')->orWhere('active', true);
            })
            ->orderByDesc('id_product')
            ->limit($limit)
            ->get()
            ->map(fn (ProductModel $p) => $this->mapProductModel($p))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function recommendations(Request $request): JsonResponse
    {
        return $this->popular($request);
    }

    private function mapProductModel(ProductModel $p): array
    {
        return [
            'id' => (int) $p->id_product,
            'barcode' => (string) ($p->barcode ?? ''),
            'name' => (string) ($p->nama_product ?? ''),
            'brand' => $p->brand,
            'category' => null,
            'status' => (string) ($p->status ?? 'unknown'),
            'halal_info' => null,
            'ingredients' => $p->komposisi,
            'nutrition_facts' => null,
            'image_url' => $p->image,
            'source' => (string) ($p->source ?? 'local'),
            'created_at' => optional($p->created_at)?->toIso8601String() ?? now()->toIso8601String(),
            'updated_at' => optional($p->updated_at)?->toIso8601String() ?? now()->toIso8601String(),
        ];
    }

    private function mapExternalArrayToProductPayload(array $row): array
    {
        $barcode = (string) ($row['barcode'] ?? '');

        return [
            'id' => 0,
            'barcode' => $barcode,
            'name' => (string) ($row['name'] ?? ''),
            'brand' => $row['brand'] ?? null,
            'category' => $row['category'] ?? null,
            'status' => (string) ($row['halal_status'] ?? 'unknown'),
            'halal_info' => null,
            'ingredients' => $row['ingredients'] ?? null,
            'nutrition_facts' => null,
            'image_url' => $row['image'] ?? null,
            'source' => (string) ($row['source'] ?? 'external'),
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
