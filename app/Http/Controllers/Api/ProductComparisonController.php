<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\ProductModel;
use App\Services\UniversalProductService;

class ProductComparisonController extends Controller
{
    protected $geminiService;
    protected $universalService;

    public function __construct(GeminiService $geminiService, UniversalProductService $universalService)
    {
        $this->geminiService = $geminiService;
        $this->universalService = $universalService;
    }

    /**
     * Compare up to 3 products
     */
    public function compare(Request $request)
    {
        $request->validate([
            'barcodes' => 'required|array|min:2|max:3',
            'barcodes.*' => 'string'
        ]);

        $barcodes = $request->barcodes;
        $user = $request->user();
        $productsData = [];

        foreach ($barcodes as $barcode) {
            $result = $this->universalService->findProduct($barcode);
            if ($result['found']) {
                $productsData[] = $result['standardized'];
            } else {
                return response()->json(['error' => 'Product not found for barcode: ' . $barcode], 404);
            }
        }

        // Get user context for personalization
        $userContext = [];
        if ($user) {
            $userContext = [
                'allergies' => $user->allergy ? explode(',', $user->allergy) : [],
                'medical_history' => $user->medical_history,
                'diet_preference' => $user->diet_preference,
            ];
        }

        // Use Gemini for comparison
        $comparison = $this->geminiService->compareProducts($productsData, $userContext);

        return response()->json([
            'comparison' => $comparison,
            'products' => $productsData
        ]);
    }
}