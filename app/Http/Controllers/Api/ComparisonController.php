<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use App\Services\UniversalProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComparisonController extends Controller
{
    protected $gemini;
    protected $universalProduct;

    public function __construct(GeminiService $gemini, UniversalProductService $universalProduct)
    {
        $this->gemini = $gemini;
        $this->universalProduct = $universalProduct;
    }

    /**
     * Compare multiple products using AI
     */
    public function compare(Request $request)
    {
        $request->validate([
            'barcodes' => 'required|array|min:2|max:5',
            'barcodes.*' => 'required|string',
            'family_id' => 'nullable|integer',
        ]);

        $barcodes = $request->barcodes;
        $familyId = $request->family_id;
        $user = Auth::user();

        // 1. Resolve Health Context
        $userContext = $this->resolveHealthContext($user, $familyId);

        // 2. Fetch Product Data
        $productsData = [];
        foreach ($barcodes as $barcode) {
            $result = $this->universalProduct->findProduct($barcode);
            if ($result['found']) {
                $productsData[] = $result['standardized'];
            }
        }

        if (count($productsData) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 2 produk yang ditemukan diperlukan untuk perbandingan.'
            ], 400);
        }

        // 3. AI Comparison
        try {
            $analysis = $this->gemini->compareProducts($productsData, $userContext);

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'products' => $productsData // Return raw data too for UI display
            ]);
        } catch (\Exception $e) {
            Log::error('Comparison AI Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menganalisis perbandingan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to resolve health context for either the main user or a family member
     */
    private function resolveHealthContext($user, $familyId = null)
    {
        if ($familyId) {
            $family = \App\Models\FamilyProfile::where('user_id', $user->id_user)->find($familyId);
            if ($family) {
                return [
                    'name' => $family->name,
                    'is_family_member' => true,
                    'age' => $family->age,
                    'gender' => $family->gender,
                    'medical_history' => $family->medical_history,
                    'allergies' => $family->allergies,
                    'diabetes' => str_contains(strtolower($family->medical_history ?? ''), 'diabetes')
                ];
            }
        }

        return [
            'name' => $user->full_name,
            'is_family_member' => false,
            'age' => $user->age,
            'gender' => $user->gender,
            'medical_history' => $user->medical_history,
            'allergies' => $user->allergy,
            'diabetes' => $user->has_diabetes
        ];
    }
}
