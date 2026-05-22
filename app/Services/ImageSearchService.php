<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageSearchService
{
    private string $googleApiKey;
    private string $searchEngineId;

    public function __construct()
    {
        $this->googleApiKey = (string) config('services.google.search_key', env('GOOGLE_API_KEY', ''));
        $this->searchEngineId = (string) config('services.google.search_engine_id', env('SEARCH_ENGINE_ID', ''));
    }

    /**
     * Search for an image with fallback logic.
     * 1. Check if an image URL is already provided (e.g., from DB or local).
     * 2. If not, use Google Custom Search API to find a product image.
     * 3. If no API keys or search fails, return a default placeholder.
     */
    public function findProductImage(string $productName, ?string $existingImageUrl = null): string
    {
        // 1. Existing DB/API Image
        if (!empty($existingImageUrl) && filter_var($existingImageUrl, FILTER_VALIDATE_URL)) {
            return $existingImageUrl;
        }

        // 2. Google Custom Search
        if (!empty($this->googleApiKey) && !empty($this->searchEngineId) && !empty($productName)) {
            try {
                $response = Http::timeout(10)->get('https://www.googleapis.com/customsearch/v1', [
                    'key' => $this->googleApiKey,
                    'cx' => $this->searchEngineId,
                    'q' => $productName . ' product packaging',
                    'searchType' => 'image',
                    'num' => 1
                ]);

                if ($response->successful()) {
                    $items = $response->json('items');
                    if (!empty($items) && isset($items[0]['link'])) {
                        return $items[0]['link'];
                    }
                } else {
                    Log::error('Google Image Search Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Image Search Exception: ' . $e->getMessage());
            }
        }

        // 3. Fallback Placeholder
        // A clean generated placeholder or default image from UI
        return url('/images/placeholder_product.png');
    }
}
