<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Services\AI\CategoryDetectorService;
use App\Services\OpenFoodFactsService;
use App\Models\ScanModel;
use App\Models\FamilyProfile;
use App\Models\MedicalProfile;
use App\Services\AI\FoodAnalysisOrchestrator;
use App\Services\CrowdSourcedReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnifiedScanController extends Controller
{
    protected $universalService;
    protected $crowdService;
    protected CategoryDetectorService $categoryDetector;
    protected FoodAnalysisOrchestrator $orchestrator;

    public function __construct(
        \App\Services\UniversalProductService $universalService,
        CrowdSourcedReportService $crowdService,
        CategoryDetectorService $categoryDetector,
        FoodAnalysisOrchestrator $orchestrator
    ) {
        $this->universalService = $universalService;
        $this->crowdService = $crowdService;
        $this->categoryDetector = $categoryDetector;
        $this->orchestrator = $orchestrator;
    }

    /**
     * UNIFIED SCAN ENDPOINT
     * Priority: BpomData -> Local Cache -> OpenFoodFacts -> OpenBeautyFacts
     */
    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'family_mode' => 'nullable|boolean'
        ]);

        $barcode = $request->barcode;
        $user = $request->user();

        // Use Universal Service
        $result = $this->universalService->findProduct($barcode);

        if ($result['found']) {
            $productData = $result['data']; // Model (ProductModel or BpomData)
            $standardized = $result['standardized'];
            $source = $result['source'];

            // Record Scan Logic
            if ($source !== 'bpom' && $productData instanceof ProductModel) {
                 $this->recordScan($user, $productData);
            }

            $detectedCategory = $this->categoryDetector->detect([
                'name' => $standardized['name'] ?? '',
                'ingredients' => $standardized['ingredients_text'] ?? '',
                'category' => $standardized['category'] ?? '',
            ]);

            // 1. Primary User Analysis
            $medicalProfile = MedicalProfile::where('id_user', $user->id_user)->first();
            $userContext = [
                'user_id' => $user->id_user,
                'name' => $user->full_name,
                'age' => $user->age,
                'gender' => $user->gender,
                'medical_history' => $user->medical_history,
                'allergies' => $user->allergy,
                'thresholds' => $medicalProfile ? $medicalProfile->thresholds : null,
            ];

            $analysis = $this->orchestrator->analyzeIngredients(
                $standardized['ingredients_text'] ?? '',
                $userContext,
                array_merge($standardized, ['source' => $source])
            );

            $response = [
                'success' => true,
                'source' => $source,
                'data' => $this->formatStandardizedResponse($standardized, $source, $productData),
                'analysis' => $analysis,
                'detected_category' => $detectedCategory,
                'message' => 'Produk ditemukan (' . $source . ')',
                'needs_verification' => $source !== 'bpom' && ($productData->verification_status ?? '') !== 'verified',
            ];

            // 2. Family Box Analysis (Feature 3)
            if ($request->family_mode || $request->has('include_family')) {
                $familyMembers = FamilyProfile::where('user_id', $user->id_user)->get();
                $familyResults = [];

                foreach ($familyMembers as $member) {
                    $memberContext = [
                        'name' => $member->name,
                        'age' => $member->age,
                        'gender' => $member->gender,
                        'medical_history' => $member->medical_history,
                        'allergies' => $member->allergies,
                        'thresholds' => $member->thresholds,
                    ];

                    $memberAnalysis = $this->orchestrator->analyzeIngredients(
                        $standardized['ingredients_text'] ?? '',
                        $memberContext,
                        array_merge($standardized, ['source' => $source])
                    );

                    $familyResults[] = [
                        'member_id' => $member->id,
                        'name' => $member->name,
                        'relationship' => $member->relationship,
                        'is_safe' => $memberAnalysis['consumption_risk'] !== 'high',
                        'risk_level' => $memberAnalysis['consumption_risk'],
                        'warnings' => $memberAnalysis['personal_warnings'] ?? [],
                    ];
                }
                $response['family_box'] = $familyResults;
            }

            // Add crowd-sourced status
            if ($productData instanceof ProductModel) {
                $crowdStatus = $this->crowdService->getCrowdStatus($productData->id_product);
                $response['crowd_status'] = $crowdStatus;
            }

            return response()->json($response);
        }

        // ===== NOT FOUND =====
        return response()->json([
            'success' => false,
            'source' => 'none',
            'message' => 'Produk tidak ditemukan',
            'action' => 'manual_input',
            'instructions' => [
                'Apakah ini produk UMKM?',
                'Silakan foto kemasan untuk verifikasi'
            ]
        ], 404);
    }

    private function formatStandardizedResponse($stdData, $source, $originalData)
    {
        return [
            'id' => $originalData->id_product ?? $originalData->id ?? 0,
            'nama_product' => $stdData['name'],
            'barcode' => $stdData['barcode'],
            'image' => $stdData['image_url'],
            'halal_status' => $stdData['status_halal'],
            'verification_status' => $originalData->verification_status ?? ($source === 'bpom' ? 'verified' : 'needs_review'),
            'source' => $source,
            'is_verified' => ($originalData->verification_status ?? '') === 'verified' || $source === 'bpom',
            'komposisi' => $stdData['ingredients_text'], // String or raw
            'info_gizi' => $originalData->info_gizi ?? null,
            'kategori' => $stdData['category'],
            'brand' => $stdData['brand'] ?? 'Unknown',
            'quantity' => $stdData['quantity'] ?? null,
            'packaging' => $stdData['packaging'] ?? null,
            'labels' => $stdData['labels'] ?? null,
            'stores' => $stdData['stores'] ?? null,
            'countries' => $stdData['countries'] ?? null,
            'nutriscore' => $stdData['nutriscore'] ?? null,
            'nova_group' => $stdData['nova_group'] ?? null,
            'ai_summary' => $originalData->halal_analysis['summary'] ?? null,
        ];
    }

    /**
     * Record scan to history
     */
    private function recordScan($user, $product)
    {
        ScanModel::create([
            'user_id' => $user->id_user,
            'product_id' => $product->id_product,
            'tanggal_scan' => now(),
            'status_halal' => $product->status ?? 'syubhat',
        ]);
    }

    /**
     * Format product response
     */
    private function formatProductResponse($product)
    {
        return [
            'id' => $product->id_product,
            'nama_product' => $product->nama_product,
            'barcode' => $product->barcode,
            'image' => $product->image,
            'halal_status' => $product->status,
            'verification_status' => $product->verification_status,
            'source' => $product->source,
            'is_verified' => $product->verification_status === 'verified',
            'komposisi' => is_string($product->komposisi) ? json_decode($product->komposisi) : $product->komposisi,
            'info_gizi' => is_string($product->info_gizi) ? json_decode($product->info_gizi) : $product->info_gizi,
            'kategori' => $product->kategori ? $product->kategori->nama_kategori : 'Umum',
        ];
    }

}
