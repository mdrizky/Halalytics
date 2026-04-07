<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HalalProduct;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HalalCheckController extends Controller
{
    /**
     * Check halal status by barcode or ingredients
     */
    public function check(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'string',
        ]);

        try {
            $result = null;

            // Check by barcode first
            if ($request->barcode) {
                // Check in halal_products table
                $result = HalalProduct::where('product_barcode', $request->barcode)->first();

                // Check in products table
                if (!$result) {
                    $product = ProductModel::where('barcode', $request->barcode)->first();
                    if ($product) {
                        return response()->json([
                            'success' => true,
                            'data' => [
                                'status' => $product->halal_status ?? 'unknown',
                                'product_name' => $product->nama_product ?? $product->name ?? 'Unknown',
                                'brand' => $product->brand ?? null,
                                'reason' => 'Found in product database',
                                'source' => 'database',
                                'ingredients_analysis' => null,
                            ]
                        ]);
                    }
                }

                if ($result) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'status' => $result->halal_status ?? 'unknown',
                            'product_name' => $result->product_name ?? 'Unknown',
                            'brand' => $result->brand ?? null,
                            'certificate_number' => $result->halal_certificate_number ?? null,
                            'reason' => 'Found in halal product database',
                            'source' => 'halal_database',
                            'ingredients_analysis' => null,
                        ]
                    ]);
                }
            }

            // Check by ingredients
            if ($request->ingredients && is_array($request->ingredients)) {
                $haramIngredients = [
                    'pork', 'gelatin', 'lard', 'alcohol', 'wine', 'beer', 'bacon',
                    'ham', 'carmine', 'blood', 'babi', 'alkohol', 'minyak babi',
                ];

                $flagged = [];
                foreach ($request->ingredients as $ingredient) {
                    foreach ($haramIngredients as $haram) {
                        if (stripos($ingredient, $haram) !== false) {
                            $flagged[] = [
                                'ingredient' => $ingredient,
                                'matched' => $haram,
                                'status' => 'haram',
                            ];
                        }
                    }
                }

                $status = empty($flagged) ? 'halal' : 'haram';

                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => $status,
                        'reason' => empty($flagged)
                            ? 'No haram ingredients detected'
                            : 'Haram ingredients found: ' . implode(', ', array_column($flagged, 'ingredient')),
                        'ingredients_analysis' => $flagged,
                        'source' => 'ingredient_analysis',
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Please provide barcode or ingredients to check'
            ], 422);

        } catch (\Exception $e) {
            Log::error('HalalCheck error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check halal status: ' . $e->getMessage()
            ], 500);
        }
    }
}
