<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OCRProduct;
use App\Models\OCRProductImage;
use App\Models\Product;
use App\Models\ScanHistory;
use App\Models\FavoriteProduct;
use App\Services\OCRProcessingService;
use App\Services\HalalAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OCRController extends Controller
{
    protected $ocrService;
    protected $halalService;

    public function __construct(OCRProcessingService $ocrService, HalalAnalysisService $halalService)
    {
        $this->ocrService = $ocrService;
        $this->halalService = $halalService;
    }

    /**
     * Submit OCR data from Android
     */
    public function submitOCR(Request $request)
    {
        $request->validate([
            'front_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'back_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'ocr_text' => 'nullable|string',
            'family_member_id' => 'nullable|exists:family_profiles,id',
            'language' => 'nullable|string|in:en,id,ms,ar'
        ]);

        try {
            $user = Auth::user();
            
            // Store images
            $frontImagePath = $request->file('front_image')->store('ocr/products/front', 'public');
            $backImagePath = $request->file('back_image')->store('ocr/products/back', 'public');

            $geminiService = app(\App\Services\GeminiService::class);
            
            // 1. Resolve User/Family Context
            $userContext = [
                'name' => $user->username,
                'age' => $user->age,
                'medical_history' => $user->medical_history,
                'allergies' => $user->allergy,
                'diabetes' => $user->has_diabetes,
                'goal' => $user->goal
            ];

            if ($request->family_member_id) {
                $familyMember = \App\Models\FamilyProfile::find($request->family_member_id);
                if ($familyMember) {
                    $userContext = [
                        'name' => $familyMember->name,
                        'age' => $familyMember->age,
                        'medical_history' => $familyMember->medical_history,
                        'allergies' => $familyMember->allergies,
                        'diabetes' => $familyMember->is_diabetic,
                        'goal' => $familyMember->health_goal
                    ];
                }
            }
            
            // 2. Perform Image Analysis (Direct Vision OCR + Analysis)
            $backImageBase64 = base64_encode(file_get_contents($request->file('back_image')->path()));
            $aiAnalysis = $geminiService->analyzeIngredientsFromImage($backImageBase64, $userContext);
            
            // Fallback to text analysis if vision fails or text is provided manually
            if ((!$aiAnalysis || isset($aiAnalysis['error'])) && $request->ocr_text) {
                $aiAnalysis = $geminiService->analyzeIngredients($request->ocr_text, $userContext);
            }

            // 3. Traditional Ingredient Parsing (Hybrid)
            $ingredientsList = isset($aiAnalysis['ingredients']) ? collect($aiAnalysis['ingredients'])->pluck('name')->toArray() : [];
            $ingredientAnalysis = $this->halalService->analyzeIngredients($ingredientsList);
            
            // Determine overall status
            $overallStatus = $aiAnalysis['status_halal'] ?? 'unknown';
            
            // Create OCR product record
            $ocrProduct = OCRProduct::create([
                'user_id' => $user->id_user,
                'product_name' => $aiAnalysis['product_name'] ?? 'Unknown Product',
                'brand' => $aiAnalysis['brand'] ?? 'Unknown Brand',
                'country' => $aiAnalysis['country'] ?? 'Unknown',
                'ingredients_raw' => $request->ocr_text ?? "Analyzed from image",
                'ingredients_parsed' => json_encode($ingredientsList),
                'halal_status' => $overallStatus,
                'confidence_level' => $aiAnalysis['skor_kesehatan'] ?? 70, // Using health score as proxy or fixed confidence
                'source' => 'ocr',
                'status' => 'verified', // AI verified products can go straight to verified for display
                'ocr_text_hash' => md5($request->ocr_text ?? $backImageBase64),
                'front_image_path' => $frontImagePath,
                'back_image_path' => $backImagePath,
                'language' => $request->language ?? app()->getLocale(),
                'ai_analysis' => json_encode($aiAnalysis),
            ]);

            // Store ingredient analysis
            foreach ($ingredientAnalysis as $ingredient) {
                $ocrProduct->ingredients()->attach($ingredient['id'], [
                    'status' => $ingredient['halal_status'],
                    'risk_level' => $ingredient['risk_level'] ?? 'medium',
                    'source' => 'ocr'
                ]);
            }

            // Add to scan history
            ScanHistory::create([
                'user_id' => $user->id_user,
                'product_id' => $ocrProduct->id,
                'product_type' => 'ocr',
                'product_name' => $ocrProduct->product_name,
                'barcode' => null,
                'status' => $overallStatus,
                'scanned_at' => now()
            ]);

            // Format Ingredients for Android (simulate pivot structure)
            $formattedIngredients = collect($ingredientAnalysis)->map(function($ing) {
                return [
                    'id' => $ing['id'],
                    'name' => $ing['name'],
                    'pivot' => [
                        'status' => $ing['halal_status'],
                        'risk_level' => $ing['risk_level'] ?? 'medium'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $ocrProduct->id,
                    'product_name' => $ocrProduct->product_name,
                    'brand' => $ocrProduct->brand,
                    'halal_status' => $ocrProduct->halal_status,
                    'ai_analysis' => $aiAnalysis,
                    'front_image_url' => asset('storage/' . $frontImagePath),
                    'back_image_url' => asset('storage/' . $backImagePath),
                    'created_at' => now()->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OCR processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get OCR product details
     */
    public function getOCRProduct($id)
    {
        $product = OCRProduct::with(['ingredients', 'user'])->find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update OCR product (admin only)
     */
    public function updateOCRProduct(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string',
            'brand' => 'nullable|string',
            'country' => 'nullable|string',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'status' => 'required|in:pending_admin_review,verified,rejected'
        ]);

        $product = OCRProduct::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->update([
            'product_name' => $request->product_name,
            'brand' => $request->brand,
            'country' => $request->country,
            'halal_status' => $request->halal_status,
            'status' => $request->status,
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Get all OCR products for admin
     */
    public function getAdminProducts()
    {
        $products = OCRProduct::with(['user', 'ingredients'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Add product to favorites
     */
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|in:ocr,barcode,manual'
        ]);

        $user = Auth::user();
        
        // Check if already in favorites
        $exists = FavoriteProduct::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('product_type', $request->product_type)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in favorites'
            ], 400);
        }

        FavoriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'product_type' => $request->product_type
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to favorites'
        ]);
    }

    /**
     * Remove from favorites
     */
    public function removeFromFavorites($productId)
    {
        $user = Auth::user();
        
        $favorite = FavoriteProduct::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Product not in favorites'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from favorites'
        ]);
    }

    /**
     * Get user's favorites
     */
    public function getFavorites()
    {
        $user = Auth::user();
        
        $favorites = FavoriteProduct::with(['ocrProduct', 'product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    /**
     * Check if OCR text already exists
     */
    public function checkDuplicateOCR(Request $request)
    {
        $request->validate([
            'ocr_text' => 'required|string'
        ]);

        $hash = md5($request->ocr_text);
        $existing = OCRProduct::where('ocr_text_hash', $hash)->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'data' => $existing
            ]);
        }

        return response()->json([
            'success' => true,
            'exists' => false
        ]);
    }

    private function determineHalalStatus($ingredients)
    {
        $hasHaram = false;
        $hasSyubhat = false;
        $hasUnknown = false;

        foreach ($ingredients as $ingredient) {
            switch ($ingredient['halal_status']) {
                case 'haram':
                    $hasHaram = true;
                    break;
                case 'syubhat':
                    $hasSyubhat = true;
                    break;
                case 'unknown':
                    $hasUnknown = true;
                    break;
            }
        }

        if ($hasHaram) return 'haram';
        if ($hasSyubhat) return 'syubhat';
        if ($hasUnknown) return 'unknown';
        return 'halal';
    }

    private function calculateConfidence($parsedData, $ingredientAnalysis)
    {
        $confidence = 0;
        
        // Product name detected
        if (!empty($parsedData['product_name'])) $confidence += 25;
        
        // Brand detected
        if (!empty($parsedData['brand'])) $confidence += 15;
        
        // Country detected
        if (!empty($parsedData['country'])) $confidence += 10;
        
        // Ingredients quality
        $totalIngredients = count($ingredientAnalysis);
        $knownIngredients = collect($ingredientAnalysis)->filter(function ($ing) {
            return $ing['halal_status'] !== 'unknown';
        })->count();
        
        if ($totalIngredients > 0) {
            $confidence += ($knownIngredients / $totalIngredients) * 50;
        }

        return min(100, $confidence);
    }

    private function generateWarnings($ingredients)
    {
        $warnings = [];
        
        foreach ($ingredients as $ingredient) {
            if ($ingredient['halal_status'] === 'haram') {
                $warnings[] = "Contains haram ingredient: {$ingredient['name']}";
            } elseif ($ingredient['halal_status'] === 'syubhat') {
                $warnings[] = "Contains doubtful ingredient: {$ingredient['name']}";
            }
        }

        return $warnings;
    }
}
