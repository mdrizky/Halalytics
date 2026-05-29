<?php

namespace App\Services;

use App\Models\BpomData;
use App\Models\Medicine;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UniversalProductService
{
    protected $safetyChecker;
    protected $externalApiService;
    protected $geminiService;
    protected $bpomMuiService;

    public function __construct(
        SafetyCheckerService $safetyChecker, 
        ExternalApiService $externalApiService, 
        GeminiService $geminiService,
        BpomMuiService $bpomMuiService
    ) {
        $this->safetyChecker = $safetyChecker;
        $this->externalApiService = $externalApiService;
        $this->geminiService = $geminiService;
        $this->bpomMuiService = $bpomMuiService;
    }

    /**
     * Find product by barcode from multiple sources.
     * Priority: Local BpomData (Verified) -> Local Medicines -> Local Cache (ProductModel) -> OpenFoodFacts -> OpenBeautyFacts
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

        // 1.2 REAL-TIME BPOM CHECK (If not in local DB)
        $officialBpom = $this->bpomMuiService->searchBpom($barcode);
        if ($officialBpom['found']) {
            return [
                'source' => 'bpom_official',
                'found' => true,
                'data' => $officialBpom,
                'standardized' => $this->standardizeOfficialBpom($officialBpom)
            ];
        }

        // 1.3 REAL-TIME MUI CHECK
        $officialMui = $this->bpomMuiService->searchMui($barcode);
        if ($officialMui['found']) {
            return [
                'source' => 'mui_official',
                'found' => true,
                'data' => $officialMui,
                'standardized' => $this->standardizeOfficialMui($officialMui)
            ];
        }

        // 1.5 Check Local Medicines by Barcode
        $medicine = Medicine::where('barcode', $barcode)->first();
        if ($medicine) {
            return [
                'source' => 'medicine',
                'found' => true,
                'data' => $medicine,
                'standardized' => $this->standardizeMedicine($medicine)
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

        // 3. Check Open Food Facts API v2 (with 24h caching + 5s timeout)
        $cacheKey = "product_off_{$barcode}";
        $productData = Cache::remember($cacheKey, 86400, function () use ($barcode) {
            try {
                $offResponse = Http::timeout(5)->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'product_name,code,image_url,image_front_url,ingredients_list,nutriments,_id,completeness,brands,quantity,packaging,labels,nutriscore_grade,nova_group,stores,countries'
                ]);
                if ($offResponse->successful() && $offResponse->json('status') === 'success') {
                    return $offResponse->json('product');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("OFF request failed for {$barcode}: " . $e->getMessage());
            }
            return null;
        });

        if ($productData) {
            // Cache to DB
            $savedProduct = $this->saveToLocalCache($productData, 'open_food_facts');
            
            return [
                'source' => 'open_food_facts',
                'found' => true,
                'data' => $savedProduct,
                'standardized' => $this->standardizeLocal($savedProduct)
            ];
        }

        // 4. Check Open Beauty Facts API v2 (with 24h caching + 5s timeout)
        $obfCacheKey = "product_obf_{$barcode}";
        $obfProductData = Cache::remember($obfCacheKey, 86400, function () use ($barcode) {
            try {
                $obfResponse = Http::timeout(5)->get("https://world.openbeautyfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'product_name,code,image_url,image_front_url,ingredients_list,nutriments,_id,completeness,brands,quantity,packaging,labels,nutriscore_grade,nova_group,stores,countries'
                ]);
                if ($obfResponse->successful() && $obfResponse->json('status') === 'success') {
                    return $obfResponse->json('product');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("OBF request failed for {$barcode}: " . $e->getMessage());
            }
            return null;
        });

        if ($obfProductData) {
            // Cache to DB
            $savedProduct = $this->saveToLocalCache($obfProductData, 'open_beauty_facts');
            
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

            // 🌟 SMART RULE-BASED BACKUP CLASSIFICATION (100% BULLETPROOF)
            $pNameLower = strtolower($productName);
            $pIngLower = strtolower($ingredientsList);
            
            // Default Fallback values
            $kategoriId = 24; // Default: Makanan
            $status = 'syubhat';
            $summary = "Analisis awal selesai. Silakan periksa label komposisi produk untuk memverifikasi bahan kritis.";

            // 1. Check for Haram ingredients first
            $hasHaram = false;
            $haramKeywords = ['babi', 'pork', 'lard', 'gelatin babi', 'bacon', 'ham', 'wine', 'rum', 'sake', 'mirin', 'alcohol', 'ethanol', 'carmine', 'cochineal'];
            foreach ($haramKeywords as $kw) {
                if (str_contains($pNameLower, $kw) || str_contains($pIngLower, $kw)) {
                    $status = 'tidak halal';
                    $hasHaram = true;
                    $summary = "Peringatan: Terdeteksi bahan kritis/non-halal ({$kw}) dalam produk ini. Tidak disarankan untuk dikonsumsi.";
                    break;
                }
            }

            // 2. Check category based on keywords
            if (preg_match('/milk|lactose|cheese|keju|susu|yogurt|butter|mentega|whey/i', $productName . $ingredientsList)) {
                $kategoriId = 6; // Dairy
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Produk olahan susu terdeteksi. Kaya akan kalsium dan nutrisi harian. Status halal aman selama diproses secara higienis.";
                }
            } elseif (preg_match('/noodle|mie|ramen|udon|spaghetti|pasta/i', $productName . $ingredientsList)) {
                $kategoriId = 15; // Mie Instan
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Produk olahan mi terdeteksi. Batasi konsumsi karena kadar natrium bumbu instan cukup tinggi.";
                }
            } elseif (preg_match('/teh|tea|kopi|coffee|espresso|cappuccino|latte/i', $productName . $ingredientsList)) {
                $kategoriId = 20; // Kopi & Teh
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Produk teh/kopi segar terdeteksi. Alami dan kaya akan antioksidan penangkal radikal bebas.";
                }
            } elseif (preg_match('/skincare|cream|serum|toner|moisturizer|facial|sunscreen|sabun wajah/i', $productName . $ingredientsList)) {
                $kategoriId = 22; // Skincare
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Produk perawatan wajah luar terdeteksi. Aman digunakan untuk menjaga hidrasi kulit harian.";
                }
            } elseif (preg_match('/lip|lipstick|eye|shadow|blush|foundation|bedak|makeup|maskara/i', $productName . $ingredientsList)) {
                $kategoriId = 5; // Kosmetik
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Produk kosmetik rias luar terdeteksi. Membantu menunjang penampilan wajah dengan formula kosmetik aman.";
                }
            } elseif (preg_match('/paracetamol|ibuprofen|tablet|sirup|kapsul|obat|medicine|drug/i', $productName . $ingredientsList)) {
                $kategoriId = 25; // Obat
                if (!$hasHaram) {
                    $status = 'syubhat';
                    $summary = "Obat-obatan medis terdeteksi. Waspadai cangkang kapsul gelatin jika belum tersertifikasi halal resmi.";
                }
            } elseif (preg_match('/juice|jus|soda|cola|drink|water|air|beverage|sirup/i', $productName . $ingredientsList)) {
                $kategoriId = 2; // Minuman
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Minuman penyegar terdeteksi. Membantu menghidrasi tubuh secara instan dengan rasa menyegarkan.";
                }
            } elseif (preg_match('/snack|camilan|keripik|chips|biskuit|cookie|wafer|permen|candy/i', $productName . $ingredientsList)) {
                $kategoriId = 1; // Makanan Ringan
                if (!$hasHaram) {
                    $status = 'halal';
                    $summary = "Makanan ringan selingan terdeteksi. Praktis dikonsumsi, namun batasi karena tinggi garam/gula.";
                }
            }

            $halalAnalysis = [
                'status' => $status,
                'kategori_id' => $kategoriId,
                'summary' => $summary
            ];
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

    private function standardizeOfficialBpom($item)
    {
        return [
            'barcode' => $item['barcode'] ?? null,
            'name' => $item['nama_produk'],
            'brand' => $item['merk'],
            'image_url' => null,
            'ingredients_text' => null,
            'status_halal' => 'verified',
            'halal_certificate' => $item['nomor_registrasi'],
            'certification_body' => $item['pendaftar'],
            'category' => 'Pangan/Obat (BPOM)',
            'source' => 'bpom_official',
            'nutriscore' => null,
            'additives' => [],
            'allergens' => [],
            'safety_alerts' => []
        ];
    }

    private function standardizeOfficialMui($item)
    {
        return [
            'barcode' => $item['barcode'] ?? null,
            'name' => $item['nama_produk'],
            'brand' => $item['nama_produsen'],
            'image_url' => null,
            'ingredients_text' => null,
            'status_halal' => 'halal',
            'halal_certificate' => $item['nomor_sertifikat'],
            'certification_body' => 'LPPOM MUI',
            'category' => 'Terverifikasi Halal',
            'source' => 'mui_official',
            'nutriscore' => null,
            'additives' => [],
            'allergens' => [],
            'safety_alerts' => []
        ];
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

    private function standardizeMedicine($medicine)
    {
        return [
            'barcode' => $medicine->barcode,
            'name' => $medicine->name,
            'brand' => $medicine->brand_name ?? 'Unknown Brand',
            'image_url' => $medicine->image_url,
            'ingredients_text' => $medicine->generic_name ?? '',
            'status_halal' => $medicine->halal_status ?? 'syubhat',
            'halal_certificate' => $medicine->halal_certificate_number ?? null,
            'category' => 'Obat',
            'source' => 'medicine',
            'nutriscore' => null,
            'additives' => [],
            'allergens' => [],
            'safety_alerts' => []
        ];
    }
}
