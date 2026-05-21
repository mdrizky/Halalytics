<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Services\Analysis\ProductAnalysisService;
use App\Services\External\OpenFoodFactsService;
use App\Services\External\OpenBeautyFactsService;
use App\Services\External\OpenFDAService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserScanController extends Controller
{
    public function __construct(
        private readonly OpenFoodFactsService $offService,
        private readonly OpenBeautyFactsService $obfService,
        private readonly OpenFDAService $fdaService,
        private readonly ProductAnalysisService $analysisService,
    ) {}

    public function scan(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'barcode' => ['required', 'string', 'min:8', 'max:32'],
        ]);

        $barcode = $payload['barcode'];

        $product = Cache::remember("scan_product_{$barcode}", now()->addHours(6), function () use ($barcode) {
            $off = $this->offService->getByBarcode($barcode);
            if ($off) return $off;

            $obf = $this->obfService->getByBarcode($barcode);
            if ($obf) return $obf;

            $fda = $this->fdaService->getDrugByBarcode($barcode);
            if ($fda) return $fda;

            return null;
        });

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $analysis = $this->analysisService->analyze($product);

        return response()->json([
            'success' => true,
            'data' => [
                'barcode' => $barcode,
                'product' => $product,
                'analysis' => $analysis,
            ],
        ]);
    }
}
