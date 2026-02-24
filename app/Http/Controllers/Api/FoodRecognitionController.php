<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StreetFood;
use App\Models\FoodVariant;
use App\Models\UserFoodLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FoodRecognitionController extends Controller
{
    /**
     * POST /api/food/search
     * Search street foods by name or keywords
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100'
        ]);

        $query = $request->input('query');

        try {
            $foods = StreetFood::active()
                ->search($query)
                ->with(['variants' => function ($q) {
                    $q->orderBy('popularity', 'desc');
                }])
                ->orderBy('is_popular', 'desc')
                ->orderBy('search_count', 'desc')
                ->limit(15)
                ->get();

            // Increment search count for matched foods
            foreach ($foods as $food) {
                $food->incrementSearchCount();
            }

            return response()->json([
                'success' => true,
                'message' => 'Foods found successfully',
                'data' => [ // FIXED: Changed 'content' to 'data'
                    'data' => $foods->map(function ($food) {
                        return [
                            'id' => $food->id,
                            'name' => $food->name,
                            'name_en' => $food->name_en,
                            'slug' => $food->slug,
                            'category' => $food->category,
                            'description' => $food->description,
                            'calories_typical' => $food->calories_typical,
                            'calories_range' => "{$food->calories_min}-{$food->calories_max} kcal",
                            'protein' => $food->protein,
                            'carbs' => $food->carbs,
                            'fat' => $food->fat,
                            'serving_description' => $food->serving_description,
                            'halal_status' => $food->halal_status,
                            'halal_status_label' => $food->halal_status_label,
                            'health_score' => $food->health_score,
                            'health_category' => $food->health_category,
                            'image_url' => $food->image_url,
                            'is_popular' => $food->is_popular,
                            'variants' => $food->variants->map(function ($variant) {
                                return [
                                    'id' => $variant->id,
                                    'variant_name' => $variant->variant_name,
                                    'variant_type' => $variant->variant_type,
                                    'calories_modifier' => $variant->calories_modifier,
                                    'protein_modifier' => $variant->protein_modifier,
                                    'is_default' => $variant->is_default
                                ];
                            })
                        ];
                    }),
                    'total' => $foods->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Food search error: ' . $e->getMessage());
            return response()->json([
                'response_code' => 500,
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/food/analyze
     * Analyze nutrition based on food, variant, and portion
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'food_id' => 'required|integer|exists:street_foods,id',
            'variant_id' => 'nullable|integer|exists:food_variants,id',
            'portion' => 'required|numeric|min:0.25|max:5'
        ]);

        try {
            $food = StreetFood::with('variants')->findOrFail($request->food_id);
            $variant = $request->variant_id ? FoodVariant::find($request->variant_id) : null;
            $portion = $request->portion;

            // Base nutrition from food
            $calories = (float) $food->calories_typical;
            $protein = (float) $food->protein;
            $carbs = (float) $food->carbs;
            $fat = (float) $food->fat;
            $fiber = (float) ($food->fiber ?? 0);
            $sugar = (float) ($food->sugar ?? 0);
            $sodium = (float) ($food->sodium ?? 0);

            // Add variant modifiers if selected
            if ($variant) {
                $calories += (float) $variant->calories_modifier;
                $protein += (float) $variant->protein_modifier;
                $carbs += (float) $variant->carbs_modifier;
                $fat += (float) $variant->fat_modifier;
            }

            // Apply portion multiplier
            $totalCalories = round($calories * $portion);
            $totalProtein = round($protein * $portion, 1);
            $totalCarbs = round($carbs * $portion, 1);
            $totalFat = round($fat * $portion, 1);
            $totalFiber = round($fiber * $portion, 1);
            $totalSugar = round($sugar * $portion, 1);
            $totalSodium = round($sodium * $portion);

            // Calculate serving size in grams
            $servingGrams = round($food->serving_size_grams * $portion);

            // Save to user log if authenticated
            $userId = $request->user() ? $request->user()->id : null;
            if ($userId) {
                UserFoodLog::create([
                    'user_id' => $userId,
                    'street_food_id' => $food->id,
                    'food_variant_id' => $variant?->id,
                    'input_method' => $request->input('input_method', 'text'),
                    'ai_confidence' => $request->input('ai_confidence'),
                    'portion_multiplier' => $portion,
                    'total_calories' => $totalCalories,
                    'total_protein' => $totalProtein,
                    'total_carbs' => $totalCarbs,
                    'total_fat' => $totalFat,
                    'meal_type' => $request->input('meal_type')
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Analysis completed successfully',
                'data' => [ // FIXED: Changed 'content' to 'data'
                    'food_name' => $variant ? $variant->variant_name : $food->name,
                    'base_food' => $food->name,
                    'variant' => $variant ? $variant->variant_name : null,
                    'portion' => $portion,
                    'serving_size' => [
                        'grams' => $servingGrams,
                        'description' => $portion == 1 
                            ? $food->serving_description 
                            : "{$portion}x {$food->serving_description}"
                    ],
                    'nutrition' => [
                        'calories' => $totalCalories,
                        'protein' => $totalProtein,
                        'carbs' => $totalCarbs,
                        'fat' => $totalFat,
                        'fiber' => $totalFiber,
                        'sugar' => $totalSugar,
                        'sodium' => $totalSodium
                    ],
                    'halal_info' => [
                        'status' => $food->halal_status,
                        'status_label' => $food->halal_status_label,
                        'notes' => $food->halal_notes
                    ],
                    'health_info' => [
                        'score' => $food->health_score,
                        'category' => $food->health_category,
                        'notes' => $food->health_notes,
                        'tags' => $food->health_tags ?? [],
                        'recommendations' => $food->health_recommendations
                    ],
                    'disclaimer' => 'Estimasi nutrisi bersifat perkiraan dan dapat berbeda tergantung bahan serta porsi aktual.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Food analysis error: ' . $e->getMessage());
            return response()->json([
                'response_code' => 500,
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/food/recognize-image
     * Recognize food from uploaded image
     */
    /**
     * POST /api/food/recognize-image
     * Recognize food from uploaded image using Real AI + Database Matching
     */
    public function recognizeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        try {
            $image = $request->file('image');
            // Convert to Base64 for Gemini
            $base64Image = base64_encode(file_get_contents($image->getPathname()));
            
            // Save locally for history/display (optional, but good for UX)
            $path = $image->store('food-scans', 'public');
            
            // 1. Ask Gemini to identify
            $geminiService = app(\App\Services\GeminiService::class);
            $aiResult = $geminiService->identifyFoodCandidates($base64Image);
            
            $matches = [];
            $seenIds = [];

            if (isset($aiResult['matches']) && count($aiResult['matches']) > 0) {
                // 2. Match AI candidates with Database
                foreach ($aiResult['matches'] as $candidate) {
                    $name = $candidate['name'];
                    
                    // Simple LIKE search
                    $dbFood = StreetFood::where('name', 'LIKE', "%{$name}%")
                        ->orWhere('name_en', 'LIKE', "%{$name}%")
                        ->active()
                        ->first();
                        
                    if ($dbFood && !in_array($dbFood->id, $seenIds)) {
                        $matches[] = [
                            'id' => $dbFood->id,
                            'name' => $dbFood->name,
                            'confidence' => $candidate['confidence'],
                            'category' => $dbFood->category,
                            'image_url' => $dbFood->image_url,
                            'source' => 'database'
                        ];
                        $seenIds[] = $dbFood->id;
                    } else {
                         // AI detected something not in our DB? 
                         // For now, we only show DB matches to ensure we have nutrition data.
                         // Future: Add "Unknown Food" entry with AI estimated nutrition.
                    }
                }
            }

            // Fallback: If no matches found/confident, give recommendations or empty
            if (empty($matches)) {
                 $matches = StreetFood::active()->popular()->inRandomOrder()->limit(3)->get()->map(function($f) {
                     return [
                        'id' => $f->id,
                        'name' => $f->name,
                        'confidence' => 0.5, // Low confidence fallback
                        'category' => $f->category,
                        'image_url' => $f->image_url,
                        'source' => 'recommendation'
                     ];
                 });
            }

            return response()->json([
                'success' => true,
                'message' => 'Image analyzed successfully',
                'data' => [ // FIXED: Changed 'content' to 'data'
                    'image_path' => asset('storage/' . $path),
                    'matches' => $matches
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Food recognition error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Recognition failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/food/popular
     * Get list of popular street foods
     */
    public function popular(Request $request)
    {
        try {
            $foods = StreetFood::active()
                ->popular()
                ->with(['defaultVariant'])
                ->orderBy('search_count', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Popular foods retrieved successfully',
                'data' => [ // FIXED: Changed 'content' to 'data'
                    'data' => $foods->map(function ($food) {
                        return [
                            'id' => $food->id,
                            'name' => $food->name,
                            'name_en' => $food->name_en,
                            'category' => $food->category,
                            'calories_typical' => $food->calories_typical,
                            'halal_status' => $food->halal_status,
                            'halal_status_label' => $food->halal_status_label,
                            'health_score' => $food->health_score,
                            'image_url' => $food->image_url,
                        ];
                    }),
                    'total' => $foods->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Popular foods error: ' . $e->getMessage());
            return response()->json([
                'response_code' => 500,
                'success' => false,
                'message' => 'Failed to get popular foods: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/food/{id}
     * Get single food detail with variants
     */
    public function show($id)
    {
        try {
            $food = StreetFood::with(['variants' => function ($q) {
                $q->orderBy('popularity', 'desc');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Food retrieved successfully',
                'data' => [ // FIXED: Changed 'content' to 'data'
                    'id' => $food->id,
                    'name' => $food->name,
                    'name_en' => $food->name_en,
                    'slug' => $food->slug,
                    'category' => $food->category,
                    'description' => $food->description,
                    'nutrition' => [
                        'calories_min' => $food->calories_min,
                        'calories_max' => $food->calories_max,
                        'calories_typical' => $food->calories_typical,
                        'protein' => $food->protein,
                        'carbs' => $food->carbs,
                        'fat' => $food->fat,
                        'fiber' => $food->fiber,
                        'sugar' => $food->sugar,
                        'sodium' => $food->sodium
                    ],
                    'serving' => [
                        'size_grams' => $food->serving_size_grams,
                        'description' => $food->serving_description
                    ],
                    'halal_info' => [
                        'status' => $food->halal_status,
                        'status_label' => $food->halal_status_label,
                        'notes' => $food->halal_notes
                    ],
                    'health_info' => [
                        'score' => $food->health_score,
                        'category' => $food->health_category,
                        'notes' => $food->health_notes,
                        'tags' => $food->health_tags ?? [],
                        'recommendations' => $food->health_recommendations
                    ],
                    'image_url' => $food->image_url,
                    'common_ingredients' => $food->common_ingredients ?? [],
                    'variants' => $food->variants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'variant_name' => $variant->variant_name,
                            'variant_type' => $variant->variant_type,
                            'variant_type_label' => $variant->variant_type_label,
                            'calories_modifier' => $variant->calories_modifier,
                            'protein_modifier' => $variant->protein_modifier,
                            'carbs_modifier' => $variant->carbs_modifier,
                            'fat_modifier' => $variant->fat_modifier,
                            'is_default' => $variant->is_default
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Food detail error: ' . $e->getMessage());
            return response()->json([
                'response_code' => 404,
                'success' => false,
                'message' => 'Food not found'
            ], 404);
        }
    }

    /**
     * GET /api/food/categories
     * Get list of food categories
     */
    public function categories()
    {
        try {
            $categories = StreetFood::active()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories // FIXED: Changed 'content' to 'data'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'success' => false,
                'message' => 'Failed to get categories'
            ], 500);
        }
    }

    /**
     * GET /api/food/user-logs
     * Get user's food consumption logs
     */
    public function userLogs(Request $request)
    {
        try {
            $userId = $request->user()->id;
            
            $logs = UserFoodLog::forUser($userId)
                ->with(['streetFood', 'foodVariant'])
                ->recent(20)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Food logs retrieved successfully',
                'data' => [ // FIXED: Changed 'content' to 'data'
                    'data' => $logs->map(function ($log) {
                        return [
                            'id' => $log->id,
                            'food_name' => $log->foodVariant 
                                ? $log->foodVariant->variant_name 
                                : $log->streetFood->name,
                            'total_calories' => $log->total_calories,
                            'total_protein' => $log->total_protein,
                            'total_carbs' => $log->total_carbs,
                            'total_fat' => $log->total_fat,
                            'portion' => $log->portion_multiplier,
                            'meal_type' => $log->meal_type,
                            'meal_type_label' => $log->meal_type_label,
                            'input_method' => $log->input_method,
                            'consumed_at' => $log->consumed_at->format('Y-m-d H:i:s')
                        ];
                    }),
                    'total' => $logs->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'success' => false,
                'message' => 'Failed to get food logs'
            ], 500);
        }
    }
}
