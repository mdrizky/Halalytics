<?php

namespace App\Services;

use App\Models\BpomData;
use App\Models\Medicine;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Http;

class UniversalProductService
{
    protected $safetyChecker;
    protected $externalApiService;
    protected $geminiService;

    public function __construct(SafetyCheckerService $safetyChecker, ExternalApiService $externalApiService, GeminiService $geminiService)
    {
        $this->safetyChecker = $safetyChecker;
        $this->externalApiService = $externalApiService;
        $this->geminiService = $geminiService;
    }

    /**
     * Find product by barcode from multiple sources.
     * Priority: Local BpomData (Verified) -> Local Cache (ProductModel) -> OpenFoodFacts -> OpenBeautyFacts
     */
    public function findProduct($barcode)
    {
        // 1. Check Local Verified Data (BpomData) - HIGHEST PRIORITY
        $bpomProduct = BpomData::where('barcode', $barcode)->first();
        if ($bpomProduct) {
            return [
                'source' => 'bpom',
                'found' => true,
                'data' => $bpomProduct,
                'standardized' => $this->standardizeBpom($bpomProduct)
            ];
        }

        // 2. Check Local Cache (ProductModel)
        $localProduct = ProductModel::where('barcode', $barcode)->first();
        if ($localProduct) {
             // If data is from OFF and old, we might want to refresh?
             // For now, return cached.
            return [
                'source' => $localProduct->source ?? 'local_cache',
                'found' => true,
                'data' => $localProduct,
                'standardized' => $this->standardizeLocal($localProduct)
            ];
        }

        // 3. Check Open Food Facts API v2
        $offResponse = Http::get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json", [
            'fields' => 'product_name,code,image_url,image_front_url,ingredients_list,nutriments,_id,completeness,brands,quantity,packaging,labels,nutriscore_grade,nova_group,stores,countries'
        ]);
        if ($offResponse->successful() && $offResponse->json('status') === 'success') {
            $productData = $offResponse->json('product');
            // Cache to DB
            $savedProduct = $this->saveToLocalCache($productData, 'open_food_facts');
            
            return [
                'source' => 'open_food_facts',
                'found' => true,
                'data' => $savedProduct,
                'standardized' => $this->standardizeLocal($savedProduct)
            ];
        }

        // 4. Check Open Beauty Facts API v2
        $obfResponse = Http::get("https://world.openbeautyfacts.org/api/v2/product/{$barcode}.json", [
            'fields' => 'product_name,code,image_url,image_front_url,ingredients_list,nutriments,_id,completeness,brands,quantity,packaging,labels,nutriscore_grade,nova_group,stores,countries'
        ]);
        if ($obfResponse->successful() && $obfResponse->json('status') === 'success') {
            $productData = $obfResponse->json('product');
            // Cache to DB
            $savedProduct = $this->saveToLocalCache($productData, 'open_beauty_facts');
            
            return [
                'source' => 'open_beauty_facts',
                'found' => true,
                'data' => $savedProduct,
                'standardized' => $this->standardizeLocal($savedProduct)
            ];
        }

        return [
            'source' => 'none',
            'found' => false,
            'message' => 'Product not found'
        ];
    }

    private function saveToLocalCache($data, $source)
    {
        // Check if exists (double check)
        $existing = ProductModel::where('barcode', $data['code'] ?? '')->first();
        if ($existing) return $existing;

        $productName = $data['product_name'] ?? 'Unknown Product';
        $ingredientsList = isset($data['ingredients_list']) ? json_encode($data['ingredients_list']) : '';

        // Auto-categorization and Halal Status using AI
        $kategoriId = null;
        $status = 'syubhat';
        
        try {
            // Hardcoded active categories for AI context
            $categoriesJson = '{"1":"Makanan Ringan","2":"Minuman","3":"Bumbu Dapur","4":"Kesehatan","5":"Kosmetik","6":"Dairy","7":"Makanan Beku","8":"Sereal & Sarapan","9":"Bayi & Anak","10":"Saus & Dressing","11":"Roti & Bakery","12":"Seafood Olahan","13":"Herbal & Jamu","14":"Frozen Snack","15":"Mie Instan","16":"Roti & Kue","17":"Suplemen","18":"Baby Food","19":"Daging Olahan","20":"Kopi & Teh","21":"Saus & Sambal","22":"Skincare","23":"Makanan Kaleng","24":"Makanan","25":"Obat"}';
            
            $prompt = "Analyze this product: Name: '{$productName}', Ingredients: '{$ingredientsList}'. 
            1. Determine its halal status (halal/tidak halal/syubhat). 
            2. Match it to the best category ID from this list: {$categoriesJson}. 
            3. Provide a brief health & halal analysis summary (max 2 sentences).
            Format exactly as JSON: {\"status\": \"halal/tidak halal/syubhat\", \"kategori_id\": ID_NUMBER, \"summary\": \"Analysis text\"}";
            
            $analysis = $this->geminiService->generateCustomContent($prompt);
            
            if (isset($analysis['status']) && in_array($analysis['status'], ['halal', 'tidak halal', 'syubhat'])) {
                $status = $analysis['status'];
            }
            if (isset($analysis['kategori_id']) && is_numeric($analysis['kategori_id'])) {
                $kategoriId = (int) $analysis['kategori_id'];
            }
            $halalAnalysis = $analysis; // Store full object
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("AI Analysis failed for product {$productName}: " . $e->getMessage());
        }

        return ProductModel::create([
            'nama_product' => $productName,
            'barcode' => $data['code'] ?? '',
            'image' => $data['image_url'] ?? $data['image_front_url'] ?? $data['image_small_url'] ?? null,
            'komposisi' => $ingredientsList ?: null,
            'info_gizi' => isset($data['nutriments']) ? json_encode($data['nutriments']) : null,
            'source' => $source,
            'off_product_id' => $data['_id'] ?? null,
            'off_last_synced' => now(),
            'is_imported_from_off' => true, 
            'auto_imported_at' => now(),
            'verification_status' => 'needs_review',
            'status' => $status,
            'data_completeness_score' => $data['completeness'] ?? 0,
            'active' => true,
            'kategori_id' => $kategoriId,
            'halal_analysis' => $halalAnalysis ?? null,
            
            // New fields from API
            'brand' => $data['brands'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'packaging' => $data['packaging'] ?? null,
            'labels' => $data['labels'] ?? null,
            'nutriscore_grade' => $data['nutriscore_grade'] ?? null,
            'nova_group' => $data['nova_group'] ?? null,
            'stores' => $data['stores'] ?? null,
            'countries' => $data['countries'] ?? null,
        ]);
    }

    private function standardizeBpom($product)
    {
        return [
            'barcode' => $product->barcode,
            'name' => $product->nama_produk,
            'brand' => $product->merk ?? $product->pendaftar,
            'image_url' => $product->image_url,
            'ingredients_text' => $product->ingredients_text,
            'status_halal' => 'verified',
            'halal_certificate' => $product->nomor_reg,
            'certification_body' => $product->pendaftar,
            'category' => $product->kategori,
            'source' => 'bpom',
            'nutriscore' => null,
            'additives' => [],
            'allergens' => [],
            'safety_alerts' => $this->safetyChecker->checkIngredients($product->ingredients_text ?? '')
        ];
    }

    private function standardizeLocal($product)
    {
        $ingredients = json_decode($product->komposisi) ?? $product->komposisi;
        if (is_array($ingredients)) {
             $ingredientsText = implode(', ', $ingredients);
        } else {
             $ingredientsText = (string)$ingredients;
        }

        return [
            'barcode' => $product->barcode,
            'name' => $product->nama_product,
            'brand' => $product->brand ?? 'Unknown',
            'image_url' => $product->image,
            'ingredients_text' => $ingredientsText,
            'status_halal' => $product->status,
            'halal_certificate' => $product->halal_certificate,
            'category' => $product->kategori ? $product->kategori->nama_kategori : 'Umum',
            'source' => $product->source ?? 'local_cache',
            'nutriscore' => $product->nutriscore_grade,
            'nova_group' => $product->nova_group,
            'quantity' => $product->quantity,
            'packaging' => $product->packaging,
            'labels' => $product->labels,
            'stores' => $product->stores,
            'countries' => $product->countries,
            'additives' => [],
            'allergens' => [],
            'safety_alerts' => $this->safetyChecker->checkIngredients($ingredientsText)
        ];
    }
    public function search($query)
    {
        $results = collect();

        // 1. Local Verified (BPOM)
        $bpom = BpomData::where('nama_produk', 'like', "%{$query}%")
            ->orWhere('merk', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return $this->formatForSearch($item, 'bpom');
            });
        $results = $results->merge($bpom);

        // 2. Local Cache (ProductModel)
        $local = ProductModel::where('nama_product', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return $this->formatForSearch($item, $item->source ?? 'local_cache');
            });
        $results = $results->merge($local);

        // 3. Local Medicines (for unified user/admin search)
        $localMedicines = Medicine::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('generic_name', 'like', "%{$query}%")
                    ->orWhere('brand_name', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return $this->formatMedicineForSearch($item, $item->source ?? 'local_medicine');
            });
        $results = $results->merge($localMedicines);

        // 4. External (OpenFoodFacts) - Only if we have few results
        if ($results->count() < 10) {
            try {
                $url = "https://world.openfoodfacts.org/cgi/search.pl?search_terms=" . urlencode($query) . "&search_simple=1&action=process&json=1&page_size=5";
                $response = Http::get($url);
                
                if ($response->successful()) {
                    $products = $response->json('products');
                    if (is_array($products)) {
                        $external = collect($products)->map(function ($item) {
                            return $this->formatOFFForSearch($item);
                        });
                        $results = $results->merge($external);
                    }
                }
            } catch (\Exception $e) {
                // Ignore external search errors
            }
        }

        // 5. OpenFDA (medicine)
        if (!is_numeric($query) && $results->count() < 15) {
            try {
                $fdaResult = $this->externalApiService->searchOpenFDA($query);
                if ($fdaResult['found'] ?? false) {
                    $medicine = $this->externalApiService->upsertMedicineFromOpenFDA($fdaResult, $query);
                    if ($medicine) {
                        $results->push($this->formatMedicineForSearch($medicine, 'openfda'));
                    }
                }
            } catch (\Exception $e) {
                // Ignore external search errors
            }
        }

        // Unique by barcode to avoid duplicates
        return $results->unique(function ($item) {
            $barcode = $item['barcode'] ?? null;
            if ($barcode) {
                return 'barcode:' . $barcode;
            }
            return 'name:' . strtolower($item['nama_product'] ?? '');
        })->values();
    }

    private function formatForSearch($model, $source)
    {
        // Return structure compatible with Android Product model expectations
        // using 0 as ID for non-db items if necessary, but here models have IDs.
        $data = [
            'id_product' => $model->id_product ?? $model->id ?? 0,
            'nama_product' => $model->nama_product ?? $model->nama_produk,
            'barcode' => $model->barcode,
            'image' => $model->image ?? $model->image_url,
            'kategori' => $model->kategori_id ?? ($model->kategori ?? 'Umum'),
            'status' => $model->status ?? ($model->status_halal ?? 'syubhat'),
            'source' => $source
        ];
        
        // Ensure image is full URL if local
        if ($data['image'] && !str_starts_with($data['image'], 'http')) {
            $data['image'] = asset($data['image']);
        }
        
        return $data;
    }

    private function formatOFFForSearch($data)
    {
        return [
            'id_product' => 0, // Not in DB yet
            'nama_product' => $data['product_name'] ?? 'Unknown Product',
            'barcode' => $data['code'] ?? '',
            'image' => $data['image_front_small_url'] ?? $data['image_url'] ?? null,
            'kategori' => 'Internasional',
            'status' => 'syubhat',
            'source' => 'open_food_facts'
        ];
    }

    private function formatMedicineForSearch($medicine, $source)
    {
        return [
            'id_product' => 0,
            'id_medicine' => $medicine->id_medicine,
            'nama_product' => $medicine->name,
            'barcode' => $medicine->barcode,
            'image' => $medicine->image_url,
            'kategori' => 'Obat',
            'status' => $medicine->halal_status ?? 'syubhat',
            'source' => $source,
            'product_type' => 'medicine',
            'generic_name' => $medicine->generic_name,
            'dosage_info' => $medicine->dosage_info,
            'frequency_per_day' => $medicine->frequency_per_day ? (int) $medicine->frequency_per_day : null,
        ];
    }
}
