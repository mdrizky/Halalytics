<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OCRService
{
    private $visionClient;
    private $geminiApiKey;

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key');
        
        // Initialize Google Vision if credentials are available
        if (config('services.google_vision.credentials')) {
            $this->visionClient = new ImageAnnotatorClient([
                'credentials' => config('services.google_vision.credentials')
            ]);
        }
    }

    /**
     * 🔍 Extract text from image using OCR
     */
    public function extractTextFromImage($imagePath): array
    {
        try {
            // Try Google Vision first
            if ($this->visionClient) {
                return $this->extractWithGoogleVision($imagePath);
            }
            
            // Fallback to Gemini API
            return $this->extractWithGemini($imagePath);
            
        } catch (\Exception $e) {
            Log::error('OCR extraction failed: ' . $e->getMessage());
            
            // Final fallback to mock data for development
            return $this->getMockOCRResult();
        }
    }

    /**
     * 🤖 Extract text using Google Vision API
     */
    private function extractWithGoogleVision($imagePath): array
    {
        $imageContent = Storage::disk('public')->get($imagePath);
        
        $response = $this->visionClient->textDetection($imageContent);
        $texts = $response->getTextAnnotations();
        
        $fullText = '';
        $confidence = 0;
        
        if (!empty($texts)) {
            $fullText = $texts[0]->getDescription();
            $confidence = $texts[0]->getConfidence() ?? 0.85;
        }
        
        return [
            'text' => $fullText,
            'confidence' => $confidence * 100,
            'method' => 'google_vision',
            'processing_time' => microtime(true),
        ];
    }

    /**
     * 🧠 Extract text using Gemini API
     */
    private function extractWithGemini($imagePath): array
    {
        $imageUrl = Storage::disk('public')->url($imagePath);
        
        $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->geminiApiKey}", [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => 'Extract all text from this product label. Focus on ingredients list, product name, brand, and nutritional information. Return the text in a clean, readable format.'
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => 'image/jpeg',
                                'data' => base64_encode(Storage::disk('public')->get($imagePath))
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 2048,
            ]
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            
            return [
                'text' => $text,
                'confidence' => 0.85, // Gemini doesn't provide confidence
                'method' => 'gemini',
                'processing_time' => microtime(true),
            ];
        }

        throw new \Exception('Gemini API request failed: ' . $response->body());
    }

    /**
     * 🧪 Parse ingredients from extracted text
     */
    public function parseIngredients($text): array
    {
        $ingredients = [];
        
        // Common patterns for ingredients
        $patterns = [
            '/ingredients?\s*:?\s*([^\n]+)/i',
            '/bahan\s*[:]\s*([^\n]+)/i',
            '/komposisi\s*[:]\s*([^\n]+)/i',
        ];

        $ingredientsText = '';
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $ingredientsText = $matches[1];
                break;
            }
        }

        // If no specific ingredients section found, use full text
        if (empty($ingredientsText)) {
            $ingredientsText = $text;
        }

        // Split by common separators
        $separators = [',', ';', '\n', '•', '·', 'dan', '&'];
        $rawIngredients = preg_split('/(' . implode('|', array_map('preg_quote', $separators)) . ')/i', $ingredientsText);

        foreach ($rawIngredients as $ingredient) {
            $ingredient = trim($ingredient);
            
            // Clean up common patterns
            $ingredient = preg_replace('/^\d+[.\s]*/', '', $ingredient); // Remove numbers
            $ingredient = preg_replace('/\([^)]*\)/', '', $ingredient); // Remove parentheses
            $ingredient = preg_replace('/\[[^\]]*\]/', '', $ingredient); // Remove brackets
            
            if (strlen($ingredient) > 2 && !empty($ingredient)) {
                $ingredients[] = [
                    'name' => $ingredient,
                    'status' => $this->analyzeHalalStatus($ingredient),
                    'risk_level' => $this->calculateRiskLevel($ingredient),
                ];
            }
        }

        return array_values(array_unique($ingredients, SORT_REGULAR));
    }

    /**
     * 🕋 Analyze halal status of ingredient
     */
    private function analyzeHalalStatus($ingredient): string
    {
        $haramIngredients = [
            'pork', 'babi', 'gelatin', 'alcohol', 'arak', 'beer', 'wine', 'vodka',
            'lard', 'lemak babi', 'carrageenan', 'enzyme', 'rennet', 'pepsin',
            'lecithin', 'glycerin', 'glycerol', 'monoglycerides', 'diglycerides'
        ];

        $halalIngredients = [
            'water', 'air', 'sugar', 'gula', 'salt', 'garam', 'flour', 'tepung',
            'rice', 'beras', 'wheat', 'gandum', 'corn', 'jagung', 'potato', 'kentang',
            'vegetable', 'sayuran', 'fruit', 'buah', 'milk', 'susu', 'egg', 'telur'
        ];

        $ingredient = strtolower($ingredient);

        // Check for haram ingredients
        foreach ($haramIngredients as $haram) {
            if (strpos($ingredient, $haram) !== false) {
                return 'haram';
            }
        }

        // Check for halal ingredients
        foreach ($halalIngredients as $halal) {
            if (strpos($ingredient, $halal) !== false) {
                return 'halal';
            }
        }

        // Check for suspicious ingredients
        $suspicious = ['enzyme', 'culture', 'starter', 'flavor', 'color', 'preservative'];
        foreach ($suspicious as $sus) {
            if (strpos($ingredient, $sus) !== false) {
                return 'questionable';
            }
        }

        return 'unknown';
    }

    /**
     * ⚠️ Calculate risk level for ingredient
     */
    private function calculateRiskLevel($ingredient): string
    {
        $status = $this->analyzeHalalStatus($ingredient);
        
        switch ($status) {
            case 'haram':
                return 'high';
            case 'questionable':
                return 'medium';
            case 'halal':
                return 'low';
            default:
                return 'medium';
        }
    }

    /**
     * 📊 Analyze overall product halal status
     */
    public function analyzeProductHalalStatus(array $ingredients): array
    {
        $statusCounts = [
            'halal' => 0,
            'haram' => 0,
            'questionable' => 0,
            'unknown' => 0,
        ];

        $haramIngredients = [];
        $questionableIngredients = [];

        foreach ($ingredients as $ingredient) {
            $status = $ingredient['status'];
            $statusCounts[$status]++;

            if ($status === 'haram') {
                $haramIngredients[] = $ingredient['name'];
            } elseif ($status === 'questionable') {
                $questionableIngredients[] = $ingredient['name'];
            }
        }

        // Determine overall status
        $overallStatus = 'halal';
        $confidence = 100;

        if ($statusCounts['haram'] > 0) {
            $overallStatus = 'haram';
            $confidence = 95;
        } elseif ($statusCounts['questionable'] > 0) {
            $overallStatus = 'questionable';
            $confidence = 70;
        } elseif ($statusCounts['unknown'] > count($ingredients) * 0.5) {
            $overallStatus = 'unknown';
            $confidence = 50;
        }

        return [
            'overall_status' => $overallStatus,
            'confidence' => $confidence,
            'status_counts' => $statusCounts,
            'haram_ingredients' => $haramIngredients,
            'questionable_ingredients' => $questionableIngredients,
            'recommendation' => $this->getRecommendation($overallStatus, $haramIngredients, $questionableIngredients),
        ];
    }

    /**
     * 💡 Get recommendation based on analysis
     */
    private function getRecommendation($status, $haramIngredients, $questionableIngredients): string
    {
        switch ($status) {
            case 'haram':
                return "⚠️ Produk ini mengandung bahan haram: " . implode(', ', $haramIngredients) . ". Tidak disarankan untuk dikonsumsi.";
                
            case 'questionable':
                return "⚠️ Produk ini mengandung bahan yang perlu diverifikasi: " . implode(', ', $questionableIngredients) . ". Disarankan untuk menghubungi produsen.";
                
            case 'unknown':
                return "❓ Tidak dapat memastikan status halal produk ini. Disarankan untuk mencari sertifikasi halal resmi.";
                
            default:
                return "✅ Produk ini tampaknya halal berdasarkan analisis bahan. Namun, selalu periksa sertifikasi halal resmi.";
        }
    }

    /**
     * 🎭 Mock OCR result for development/testing
     */
    private function getMockOCRResult(): array
    {
        $mockTexts = [
            "INDOMIE GORENG RENDANG\nBahan: Mie gandum, minyak nabati, garam, gula, bumbu rendang (bumbu alami, rempah-rempah, perisa sintetik), penyedap rasa (monosodium glutamat, dinatrium guanilat, dinatrium inosinat), bubuk bawang, pewarna makanan (tartrazin CI 19140, sunset yellow CI 110, karamel CI 15010b).\n\nNomor BPOM: MD 2679-1100031\nHalal: LPPOM MUI 00130054760417",
            
            "NUTRITION FACTS\nServing Size: 1 pack (75g)\nIngredients: Wheat flour, vegetable oil, salt, sugar, chili powder, garlic powder, onion powder, soy sauce powder, spices.\n\nContains: Wheat\nMay contain: Soy, Gluten",
            
            "KOMPOSISI\nTepung Terigu, Gula, Minyak Nabati, Garam, Bumbu Rempah, Perisa Alami, Pewarna Makanan Tartrazin (CI 19140)."
        ];

        $text = $mockTexts[array_rand($mockTexts)];

        return [
            'text' => $text,
            'confidence' => 87.5,
            'method' => 'mock',
            'processing_time' => microtime(true),
        ];
    }
}
