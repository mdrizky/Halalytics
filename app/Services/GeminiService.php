<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    private string $model;

    private array $haramKeywords = [
        'babi',
        'pork',
        'porcine',
        'lard',
        'ham',
        'bacon',
        'gelatin babi',
        'wine',
        'beer',
        'rum',
        'sake',
        'mirin',
        'cochineal',
        'carmine',
    ];

    private array $syubhatKeywords = [
        'gelatin',
        'glycerin',
        'lecithin',
        'emulsifier',
        'mono and diglycerides',
        'diglycerides',
        'monoglycerides',
        'collagen',
        'rennet',
        'enzymes',
        'stearic acid',
        'lanolin',
        'squalene',
        'alcohol',
        'ethanol',
    ];

    private array $dangerousSkincareKeywords = [
        'mercury',
        'mercuric',
        'hydroquinone',
        'tretinoin',
        'lead',
        'formaldehyde',
        'rhodamine',
        'asbestos',
        'steroid',
    ];

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key', '');
        $this->model = (string) config('services.gemini.model', 'gemini-1.5-flash');
    }

    public function generateText(string $prompt, float $temperature = 0.7, int $maxTokens = 2048): string
    {
        return $this->callText($prompt, $temperature, $maxTokens)
            ?? $this->fallbackTextResponse($prompt);
    }

    public function generateCustomContent(string $prompt, float $temperature = 0.4, int $maxTokens = 2048)
    {
        $text = $this->callText($prompt, $temperature, $maxTokens)
            ?? $this->fallbackTextResponse($prompt);

        $decoded = $this->decodeJson($text);

        return $decoded ?? $text;
    }

    public function processImagePrompt(string $base64Image, string $prompt, string $mimeType = 'image/jpeg'): string
    {
        return $this->callVisionText($prompt, $base64Image, $mimeType, 0.2, 2048)
            ?? $this->fallbackImagePromptResponse($prompt);
    }

    public function generateWithImage(string $prompt, string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        $text = $this->callVisionText($prompt, $base64Image, $mimeType, 0.2, 2048)
            ?? $this->fallbackImagePromptResponse($prompt);

        $decoded = $this->decodeJson($text);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [
            'raw_text' => trim($text),
        ];
    }

    public function analyzeFood(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        $fallback = $this->fallbackFoodAnalysis();
        $prompt = <<<PROMPT
Analisis gambar makanan ini dengan teliti.
Identifikasi setiap item makanan yang terlihat.
Estimasi berat masing-masing dalam gram.
Hitung nilai gizi per item.
Tentukan apakah ada catatan halal yang perlu diperhatikan.
Jawab HANYA dalam JSON:
{
  "food_items": [
    {
      "name": "Nama makanan",
      "weight_gram": 150,
      "calories": 200,
      "carbs": 25.5,
      "protein": 10.2,
      "fat": 8.0,
      "is_halal": true,
      "halal_note": "catatan singkat"
    }
  ],
  "total_calories": 200,
  "total_carbs": 25.5,
  "total_protein": 10.2,
  "total_fat": 8.0,
  "analysis_note": "catatan singkat"
}
PROMPT;

        $result = $this->callVisionJson($prompt, $base64Image, $mimeType, $fallback, 0.1, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeMealImage(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        $food = $this->analyzeFood($base64Image, $mimeType);
        $foodNames = collect($food['food_items'] ?? [])
            ->pluck('name')
            ->filter()
            ->implode(', ');

        $halalStatus = collect($food['food_items'] ?? [])->contains(fn ($item) => ($item['is_halal'] ?? true) === false)
            ? 'syubhat'
            : 'halal';

        return [
            'food_name' => $foodNames !== '' ? $foodNames : 'Makanan tidak teridentifikasi',
            'nutrition' => [
                'calories' => (float) ($food['total_calories'] ?? 0),
                'protein' => (float) ($food['total_protein'] ?? 0),
                'fat' => (float) ($food['total_fat'] ?? 0),
                'carbs' => (float) ($food['total_carbs'] ?? 0),
            ],
            'food_items' => $food['food_items'] ?? [],
            'halal_analysis' => [
                'status' => $halalStatus,
                'notes' => $food['analysis_note'] ?? 'Perlu cek label atau komposisi bila makanan olahan.',
            ],
            'analysis_note' => $food['analysis_note'] ?? null,
        ];
    }

    public function substituteIngredients(array $ingredients, string $context = ''): array
    {
        $fallback = $this->fallbackSubstitution($ingredients, $context);
        if (empty($ingredients)) {
            return $fallback;
        }

        $prompt = <<<PROMPT
Kamu adalah ahli gizi kuliner halal dan thoyyib.
Daftar bahan berikut perlu dicek: {$this->safeJson($ingredients)}
Konteks resep: {$context}

Cek setiap bahan:
1. Apakah halal, syubhat, atau haram
2. Jika tidak halal atau syubhat, berikan substitusi halal
3. Berikan alternatif yang lebih sehat jika memungkinkan

Jawab HANYA dalam JSON:
{
  "ingredients": [
    {
      "original": "nama bahan asli",
      "status": "halal|syubhat|haram",
      "reason": "alasan singkat",
      "halal_substitute": "pengganti halal atau null",
      "healthy_substitute": "opsi lebih sehat atau null",
      "substitute_note": "catatan singkat"
    }
  ],
  "overall_note": "catatan keseluruhan resep"
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeSymptoms(string $symptoms, array $userContext = []): array
    {
        $fallback = $this->fallbackSymptomsAnalysis($symptoms, $userContext);
        $prompt = <<<PROMPT
Kamu adalah asisten kesehatan edukatif Halalytics.
Keluhan user: {$symptoms}
Konteks profil: {$this->safeJson($userContext)}

Jawab HANYA JSON dengan struktur lengkap berikut:
{
  "ringkasan_keluhan": "string",
  "severity": "mild|moderate|emergency",
  "tingkat_keparahan_label": "Ringan|Sedang|Perlu perhatian medis",
  "alasan_keparahan": "string",
  "condition": "kemungkinan kondisi utama",
  "possible_causes": [
    {"name": "string", "percentage": 60, "reason": "string"}
  ],
  "gejala_terkait": ["string"],
  "disease_explanations": [
    {"name": "string", "description": "string", "relation_to_case": "string"}
  ],
  "trigger_factors": ["string"],
  "recommended_ingredients": ["string"],
  "recommended_medicines_list": [
    {
      "name": "string",
      "function": "string",
      "dosage": "string",
      "how_to_take": "string",
      "duration": "string",
      "when_to_take": "string",
      "halal_status": "string",
      "safety_note": "string",
      "side_effects": ["string"]
    }
  ],
  "dosage_guidelines": "string",
  "drug_mechanism": "string",
  "halal_check": {"status": "string", "notes": "string"},
  "usage_instructions": "string",
  "lifestyle_advice": "string",
  "first_aid_steps": ["string"],
  "prevention": ["string"],
  "emergency_warning": "string",
  "follow_up_questions": ["string"],
  "confidence_level": "Rendah|Sedang|Tinggi",
  "recommendation": "string",
  "tldr": "string"
}

Penting:
- Edukatif, bukan diagnosis final
- Sertakan detail yang panjang namun aman
- Jika obat resep disebut, beri catatan konsultasi dokter
- Untuk status halal, jika tidak pasti tulis perlu cek label/sertifikasi
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 4096);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeIngredients(string $text, array $userContext = []): array
    {
        $fallback = $this->fallbackIngredientAnalysis($text, $userContext);
        if (trim($text) === '') {
            return $fallback;
        }

        $prompt = <<<PROMPT
Analisis daftar bahan berikut untuk aspek halal, keamanan umum, dan kecocokan profil kesehatan.
Daftar bahan: {$text}
Profil pengguna: {$this->safeJson($userContext)}

Jawab HANYA dalam JSON:
{
  "status": "halal|syubhat|haram",
  "status_halal": "halal|syubhat|haram",
  "ringkasan": "string",
  "recommendation": "string",
  "ingredients": [
    {
      "name": "string",
      "status_halal": "halal|syubhat|haram",
      "risk_level": "low|medium|high",
      "note": "string"
    }
  ],
  "nutrition_estimate": {
    "sugar_g": 0,
    "sodium_mg": 0,
    "calories": 0
  },
  "health_risk": "low|moderate|high",
  "watchouts": ["string"],
  "bahan_syubhat": ["string"]
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeIngredientsFromImage(string $base64Image, array $userContext = [], string $mimeType = 'image/jpeg'): array
    {
        $fallback = [
            'product_name' => 'Produk hasil OCR',
            'brand' => 'Belum diketahui',
            'country' => 'Belum diketahui',
            'raw_text' => '',
            'ingredients' => [],
            'status_halal' => 'unknown',
            'skor_kesehatan' => 70,
            'summary' => 'Teks belum terbaca dengan jelas. Silakan ulangi scan atau isi bahan secara manual.',
            'alerts' => [],
        ];

        $prompt = <<<PROMPT
Baca label produk dari gambar ini dan ekstrak informasi utamanya.
Jawab HANYA dalam JSON:
{
  "product_name": "string",
  "brand": "string",
  "country": "string",
  "raw_text": "seluruh teks penting yang terbaca",
  "ingredients": [{"name": "string"}],
  "status_halal": "halal|syubhat|haram|unknown",
  "skor_kesehatan": 70,
  "summary": "string",
  "alerts": ["string"]
}
PROMPT;

        $result = $this->callVisionJson($prompt, $base64Image, $mimeType, $fallback, 0.1, 3072);
        $merged = $this->mergeStructured($fallback, $result);
        $ingredientText = trim((string) ($merged['raw_text'] ?? ''));
        $analysis = $this->analyzeIngredients($ingredientText, $userContext);

        return array_merge($merged, [
            'ingredients' => $analysis['ingredients'] ?? ($merged['ingredients'] ?? []),
            'status_halal' => $analysis['status_halal'] ?? ($merged['status_halal'] ?? 'unknown'),
            'skor_kesehatan' => $this->scoreFromHalalStatus($analysis['status_halal'] ?? 'unknown'),
            'summary' => $analysis['ringkasan'] ?? ($merged['summary'] ?? ''),
            'alerts' => array_values(array_unique(array_merge(
                $merged['alerts'] ?? [],
                $analysis['watchouts'] ?? []
            ))),
        ]);
    }

    public function compareProducts(array $productsData, array $userContext = []): array
    {
        $fallback = $this->fallbackComparison($productsData, $userContext);
        if (empty($productsData)) {
            return $fallback;
        }

        $prompt = <<<PROMPT
Bandingkan produk-produk berikut untuk user Halalytics.
Produk: {$this->safeJson($productsData)}
Profil user: {$this->safeJson($userContext)}

Jawab HANYA JSON:
{
  "summary": "string",
  "recommendation": "string",
  "winner": {"name": "string", "reason": "string"},
  "products": [
    {
      "name": "string",
      "halal_score": 0,
      "nutrition_score": 0,
      "safety_score": 0,
      "profile_score": 0,
      "overall_score": 0,
      "strengths": ["string"],
      "warnings": ["string"]
    }
  ]
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 3072);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeSkincareIngredients(string $text, array $userContext = []): array
    {
        $fallback = $this->fallbackSkincareAnalysis($text, $userContext);
        if (trim($text) === '') {
            return $fallback;
        }

        $prompt = <<<PROMPT
Analisis bahan skincare/kosmetik berikut untuk keamanan dan status halal.
Bahan: {$text}
Profil user: {$this->safeJson($userContext)}

Jawab HANYA JSON:
{
  "status_keamanan": "aman|perhatian|bahaya",
  "skor_keamanan": 0,
  "status_halal": "halal|syubhat|haram",
  "ringkasan": "string",
  "bahan_berisiko": [
    {"name": "string", "risk_level": "warning|danger", "reason": "string"}
  ],
  "bahan_syubhat": ["string"],
  "rekomendasi": ["string"],
  "disclaimer": "string"
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeProductSafety(string $productName, string $ingredientsText = '', string $category = 'umum', array $userContext = []): array
    {
        $baseAnalysis = Str::contains(Str::lower($category), ['kosmetik', 'skincare'])
            ? $this->analyzeSkincareIngredients($ingredientsText, $userContext)
            : $this->analyzeIngredients($ingredientsText, $userContext);

        $fallback = [
            'nama_produk' => $productName,
            'kategori' => $category,
            'status_keamanan' => $baseAnalysis['status_keamanan'] ?? $this->healthRiskToSafetyStatus($baseAnalysis['health_risk'] ?? 'low'),
            'skor_keamanan' => $baseAnalysis['skor_keamanan'] ?? $this->scoreFromHalalStatus($baseAnalysis['status_halal'] ?? 'unknown'),
            'status_halal' => $baseAnalysis['status_halal'] ?? 'unknown',
            'alasan_halal' => $baseAnalysis['ringkasan'] ?? 'Perlu verifikasi tambahan bila produk belum memiliki sertifikasi halal resmi.',
            'ingredients_detected' => $baseAnalysis['ingredients'] ?? [],
            'bahan_berisiko' => $baseAnalysis['bahan_berisiko'] ?? [],
            'personal_alerts' => $baseAnalysis['watchouts'] ?? [],
            'ringkasan' => $baseAnalysis['ringkasan'] ?? 'Analisis produk selesai.',
            'rekomendasi' => $baseAnalysis['recommendation'] ?? 'Cek label lengkap dan izin edar resmi sebelum digunakan.',
        ];

        $prompt = <<<PROMPT
Analisis keamanan produk berikut untuk aplikasi Halalytics.
Nama produk: {$productName}
Kategori: {$category}
Daftar bahan: {$ingredientsText}
Profil user: {$this->safeJson($userContext)}

Jawab HANYA JSON:
{
  "nama_produk": "{$productName}",
  "kategori": "{$category}",
  "status_keamanan": "aman|perhatian|bahaya",
  "skor_keamanan": 0,
  "status_halal": "halal|syubhat|haram",
  "alasan_halal": "string",
  "ringkasan": "string",
  "rekomendasi": "string",
  "personal_alerts": ["string"]
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function identifyPill(string $base64Image, ?string $shape = null, ?string $color = null, string $mimeType = 'image/jpeg'): array
    {
        $fallback = [
            'possible_drugs' => [
                [
                    'name' => 'Paracetamol 500mg',
                    'generic_name' => 'Paracetamol',
                    'confidence' => 0.63,
                    'reason' => 'Pil bulat terang sering ditemukan pada analgesik umum, namun perlu verifikasi kemasan.',
                ],
                [
                    'name' => 'Cetirizine 10mg',
                    'generic_name' => 'Cetirizine',
                    'confidence' => 0.42,
                    'reason' => 'Beberapa antihistamin memiliki bentuk serupa sehingga butuh cek imprint.',
                ],
            ],
            'visual_features' => [
                'shape' => $shape ?: 'unknown',
                'color' => $color ?: 'unknown',
                'imprint' => null,
            ],
            'warning' => 'Identifikasi pil berbasis gambar bersifat indikatif. Cocokkan dengan blister/etiket sebelum dikonsumsi.',
        ];

        $prompt = <<<PROMPT
Identifikasi pil/obat dari gambar.
Petunjuk bentuk: {$shape}
Petunjuk warna: {$color}

Jawab HANYA JSON:
{
  "possible_drugs": [
    {
      "name": "string",
      "generic_name": "string",
      "confidence": 0.8,
      "reason": "string"
    }
  ],
  "visual_features": {
    "shape": "string",
    "color": "string",
    "imprint": "string"
  },
  "warning": "string"
}
PROMPT;

        $result = $this->callVisionJson($prompt, $base64Image, $mimeType, $fallback, 0.1, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function checkDrugInteraction(array $drugA, array $drugB): array
    {
        $fallback = $this->fallbackDrugInteraction($drugA, $drugB);
        $prompt = <<<PROMPT
Analisis potensi interaksi obat berikut.
Obat A: {$this->safeJson($drugA)}
Obat B: {$this->safeJson($drugB)}

Jawab HANYA JSON:
{
  "has_interaction": true,
  "severity": "minor|moderate|major",
  "description": "string",
  "recommendation": "string",
  "scientific_basis": "string",
  "sources": ["string"],
  "disclaimer": "string"
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function findHalalAlternative(array $medicine): array
    {
        $fallback = $this->fallbackMedicineAlternative($medicine);
        $prompt = <<<PROMPT
Berikan alternatif halal untuk obat berikut.
Obat: {$this->safeJson($medicine)}

Jawab HANYA JSON:
{
  "original_medicine": "string",
  "status": "halal|syubhat|haram",
  "reason": "string",
  "alternatives": [
    {
      "name": "string",
      "reason": "string",
      "notes": "string"
    }
  ],
  "overall_note": "string"
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function findProductHalalAlternative(string $productName, string $ingredients = '', string $category = ''): array
    {
        $fallback = $this->fallbackProductAlternative($productName, $ingredients, $category);
        $prompt = <<<PROMPT
Berikan alternatif halal atau lebih aman untuk produk berikut.
Nama produk: {$productName}
Kategori: {$category}
Komposisi: {$ingredients}

Jawab HANYA JSON:
{
  "original_product": "string",
  "status": "halal|syubhat|haram",
  "reason": "string",
  "alternatives": [
    {
      "name": "string",
      "reason": "string",
      "notes": "string"
    }
  ],
  "overall_note": "string"
}
PROMPT;

        $result = $this->callTextJson($prompt, $fallback, 0.2, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    public function analyzeLabResult(?string $base64Image, array $manualData = []): array
    {
        $fallback = $this->fallbackLabAnalysis($manualData);

        if ($base64Image === null || trim($base64Image) === '') {
            return $fallback;
        }

        $prompt = <<<PROMPT
Analisis hasil laboratorium dari gambar atau data yang diberikan.
Data manual: {$this->safeJson($manualData)}

Jawab HANYA JSON:
{
  "ringkasan_kondisi": "string",
  "saran_gaya_hidup": "string",
  "urgensi": "Ya|Tidak",
  "detected_tests": [
    {
      "test_name": "string",
      "value": 0,
      "unit": "string",
      "reference_range": "string",
      "status": "Normal|Tinggi|Rendah",
      "interpretation": "string"
    }
  ],
  "poin_perhatian": [
    {
      "parameter": "string",
      "nilai": 0,
      "rujukan": "string",
      "status": "Normal|Tinggi|Rendah",
      "penjelasan": "string"
    }
  ]
}
PROMPT;

        $result = $this->callVisionJson($prompt, $base64Image, 'image/jpeg', $fallback, 0.1, 3072);

        return $this->mergeStructured($fallback, $result);
    }

    public function identifyFoodCandidates(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        $fallback = [
            'matches' => [
                ['name' => 'Nasi Goreng', 'confidence' => 0.66],
                ['name' => 'Mie Goreng', 'confidence' => 0.58],
                ['name' => 'Bakso', 'confidence' => 0.47],
            ],
        ];

        $prompt = <<<PROMPT
Identifikasi maksimal 5 makanan Indonesia yang paling mungkin dari gambar.
Jawab HANYA JSON:
{
  "matches": [
    {"name": "Nasi Goreng", "confidence": 0.8}
  ]
}
PROMPT;

        $result = $this->callVisionJson($prompt, $base64Image, $mimeType, $fallback, 0.1, 2048);

        return $this->mergeStructured($fallback, $result);
    }

    private function hasApiKey(): bool
    {
        return trim($this->apiKey) !== '';
    }

    private function callText(string $prompt, float $temperature = 0.3, int $maxTokens = 2048): ?string
    {
        return $this->requestGemini(
            [
                ['text' => $prompt],
            ],
            $temperature,
            null,
            $maxTokens
        );
    }

    private function callTextJson(string $prompt, array $fallback, float $temperature = 0.2, int $maxTokens = 2048): array
    {
        $text = $this->requestGemini(
            [
                ['text' => $prompt],
            ],
            $temperature,
            'application/json',
            $maxTokens
        );

        $decoded = $this->decodeJson($text);

        return is_array($decoded) ? $decoded : $fallback;
    }

    private function callVisionText(string $prompt, string $base64Image, string $mimeType = 'image/jpeg', float $temperature = 0.2, int $maxTokens = 2048): ?string
    {
        return $this->requestGemini(
            [
                ['text' => $prompt],
                [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $this->stripBase64Header($base64Image),
                    ],
                ],
            ],
            $temperature,
            null,
            $maxTokens
        );
    }

    private function callVisionJson(string $prompt, string $base64Image, string $mimeType, array $fallback, float $temperature = 0.2, int $maxTokens = 2048): array
    {
        $text = $this->requestGemini(
            [
                ['text' => $prompt],
                [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $this->stripBase64Header($base64Image),
                    ],
                ],
            ],
            $temperature,
            'application/json',
            $maxTokens
        );

        $decoded = $this->decodeJson($text);

        return is_array($decoded) ? $decoded : $fallback;
    }

    private function requestGemini(array $parts, float $temperature = 0.3, ?string $responseMimeType = null, int $maxTokens = 2048): ?string
    {
        if (!$this->hasApiKey()) {
            Log::warning('Gemini: No API key configured. All AI features will use fallback templates.');
            return null;
        }

        // Model fallback chain — if primary model is deprecated, try alternatives
        $modelsToTry = array_unique(array_filter([
            $this->model,
            'gemini-2.0-flash-lite',
            'gemini-2.0-flash',
        ]));

        $payload = [
            'contents' => [
                [
                    'parts' => $parts,
                ],
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        if ($responseMimeType !== null) {
            $payload['generationConfig']['response_mime_type'] = $responseMimeType;
        }

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::timeout(45)
                    ->retry(1, 500)
                    ->post(
                        "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}",
                        $payload
                    );

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');

                    // If we succeeded with a fallback model, update for future calls
                    if ($model !== $this->model) {
                        Log::info("Gemini: Model '{$this->model}' unavailable, succeeded with '{$model}'");
                        $this->model = $model;
                    }

                    return is_string($text) ? trim($text) : null;
                }

                $status = $response->status();
                $errorBody = $response->body();

                // 404 = model deprecated/not found → try next model
                if ($status === 404) {
                    Log::warning("Gemini: Model '{$model}' not found (404), trying next model...", [
                        'hint' => 'This model may have been deprecated. Update GEMINI_MODEL in .env',
                    ]);
                    continue;
                }

                // 403 = API key revoked/leaked → no point retrying
                if ($status === 403) {
                    Log::error('Gemini: API key is REVOKED or INVALID (403). Generate a new key at https://aistudio.google.com/apikey', [
                        'body' => Str::limit($errorBody, 300),
                    ]);
                    return null;
                }

                // 429 = rate limit → try next model (different models may have separate quotas)
                if ($status === 429) {
                    Log::warning("Gemini: Rate limit exceeded for '{$model}' (429), trying next model...");
                    continue;
                }

                // Other errors
                Log::warning("Gemini: API call failed for model '{$model}'", [
                    'status' => $status,
                    'body' => Str::limit($errorBody, 500),
                ]);
                continue;

            } catch (\Throwable $throwable) {
                Log::warning("Gemini: Exception with model '{$model}'", [
                    'message' => $throwable->getMessage(),
                ]);
                continue;
            }
        }

        Log::error('Gemini: ALL models failed. AI features will use fallback templates. Please check your API key and model settings.', [
            'models_tried' => $modelsToTry,
        ]);
        return null;
    }

    private function decodeJson(?string $text): ?array
    {
        if (!is_string($text) || trim($text) === '') {
            return null;
        }

        $sanitized = $this->sanitizeJson($text);
        $decoded = json_decode($sanitized, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded)
            ? $decoded
            : null;
    }

    private function sanitizeJson(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```/i', '', $text);

        $firstBrace = strpos($text, '{');
        $firstBracket = strpos($text, '[');

        if ($firstBrace === false && $firstBracket === false) {
            return trim($text);
        }

        $start = $firstBrace;
        if ($start === false || ($firstBracket !== false && $firstBracket < $start)) {
            $start = $firstBracket;
        }

        $text = substr($text, $start);

        $lastBrace = strrpos($text, '}');
        $lastBracket = strrpos($text, ']');
        $end = $lastBrace;
        if ($end === false || ($lastBracket !== false && $lastBracket > $end)) {
            $end = $lastBracket;
        }

        if ($end !== false) {
            $text = substr($text, 0, $end + 1);
        }

        return trim($text);
    }

    private function stripBase64Header(string $base64): string
    {
        return str_contains($base64, ',')
            ? trim((string) Str::after($base64, ','))
            : trim($base64);
    }

    private function mergeStructured(array $fallback, array $candidate): array
    {
        return array_replace_recursive($fallback, $candidate);
    }

    private function safeJson($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    private function fallbackTextResponse(string $prompt): string
    {
        $lower = Str::lower($prompt);

        if (Str::contains($lower, ['json array', 'format kembalian wajib json array', 'maksimal 3 langkah p3k'])) {
            return json_encode([
                'Hubungi bantuan medis atau 119 jika kondisi memburuk.',
                'Pastikan pasien berada di posisi aman dan jalan napas tidak terhalang.',
                'Pantau kesadaran, napas, dan perdarahan sambil menunggu bantuan.',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (Str::contains($lower, ['list json', 'ekstrak daftar bahan aktif utama'])) {
            return json_encode(
                $this->extractActiveIngredientsFromPrompt($prompt),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        if (Str::contains($lower, ['berikan json: {bmi', '"bmi"', 'status: string, recommendations'])) {
            preg_match('/usia:\s*([\d\.]+)/i', $prompt, $ageMatch);
            preg_match('/tinggi:\s*([\d\.]+)/i', $prompt, $heightMatch);
            preg_match('/berat:\s*([\d\.]+)/i', $prompt, $weightMatch);
            $heightCm = (float) ($heightMatch[1] ?? 0);
            $weightKg = (float) ($weightMatch[1] ?? 0);
            $bmi = ($heightCm > 0) ? round($weightKg / (($heightCm / 100) ** 2), 1) : 0;
            $status = $bmi >= 30 ? 'obesitas' : ($bmi >= 25 ? 'overweight' : ($bmi >= 18.5 ? 'normal' : 'underweight'));
            return json_encode([
                'bmi' => $bmi,
                'status' => $status,
                'recommendations' => [
                    'Jaga pola makan seimbang dan kurangi minuman tinggi gula.',
                    'Lakukan aktivitas fisik rutin minimal 30 menit per hari.',
                    'Pantau berat badan dan lingkar perut tiap minggu.',
                ],
                'risks' => $bmi >= 25
                    ? ['Risiko metabolik meningkat bila pola makan tidak dikontrol.']
                    : ['Tetap jaga konsistensi pola hidup sehat.'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (Str::contains($lower, ['format as json', '"summary"', '"tips"', '"highlight"'])) {
            return json_encode([
                'summary' => 'Dalam periode ini, pola scan kamu menunjukkan perhatian yang baik terhadap keamanan dan kehalalan produk.',
                'tips' => [
                    'Prioritaskan produk dengan komposisi sederhana dan izin edar jelas.',
                    'Batasi produk tinggi gula, garam, atau lemak jenuh bila dikonsumsi harian.',
                    'Gunakan fitur scan sebelum membeli produk baru.',
                ],
                'highlight' => 'Konsistensi kecil setiap hari memberi dampak besar untuk kesehatan jangka panjang.',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (Str::contains($lower, ['status', 'reason']) && Str::contains($lower, ['halal', 'ingredients'])) {
            $status = $this->inferHalalStatusFromText($lower);
            return json_encode([
                'status' => $status,
                'reason' => $this->halalReason($status),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return 'Analisis sementara tersedia. Silakan gunakan hasil ini sebagai referensi awal dan verifikasi dengan sumber resmi bila diperlukan.';
    }

    private function fallbackImagePromptResponse(string $prompt): string
    {
        $lower = Str::lower($prompt);

        if (Str::contains($lower, ['hasil laboratorium', 'ringkasan_kondisi', 'poin_perhatian'])) {
            return json_encode([
                'ringkasan_kondisi' => 'Belum ada parameter yang dapat dipastikan dari gambar. Gunakan input manual atau foto yang lebih jelas.',
                'saran_gaya_hidup' => 'Ulangi pengambilan gambar dengan pencahayaan terang dan posisi tegak lurus.',
                'urgensi' => 'Tidak',
                'poin_perhatian' => [],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (Str::contains($lower, ['halal_status', 'ingredients_concern', 'health_score'])) {
            return json_encode([
                'halal_status' => 'Tidak Diketahui',
                'ingredients_concern' => [],
                'kalori' => 0,
                'gula' => 0,
                'health_score' => 0,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return json_encode([
            'raw_text' => '',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function fallbackIngredientAnalysis(string $text, array $userContext = []): array
    {
        $normalized = Str::lower($text);
        $flags = $this->detectIngredientFlags($normalized);
        $personalWarnings = $this->buildPersonalWarnings($normalized, $userContext);
        $status = $this->inferHalalStatusFromText($normalized);
        $nutritionEstimate = $this->estimateNutritionFromIngredients($normalized);

        $ingredients = collect($flags)->map(function (array $flag) {
            return [
                'name' => $flag['name'],
                'status_halal' => $flag['status_halal'],
                'risk_level' => $flag['risk_level'],
                'note' => $flag['note'],
            ];
        })->values()->all();

        if (empty($ingredients)) {
            $ingredients = collect(preg_split('/[,;\n]+/', $text))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->take(10)
                ->map(fn ($item) => [
                    'name' => $item,
                    'status_halal' => 'halal',
                    'risk_level' => 'low',
                    'note' => 'Tidak ditemukan indikator kritis dari pencocokan cepat.',
                ])
                ->values()
                ->all();
        }

        $healthRisk = match (true) {
            count($personalWarnings) >= 2 || $status === 'haram' => 'high',
            count($personalWarnings) === 1 || $status === 'syubhat' => 'moderate',
            default => 'low',
        };

        return [
            'status' => $status,
            'status_halal' => $status,
            'ringkasan' => $this->halalReason($status),
            'recommendation' => $status === 'haram'
                ? 'Produk ini sebaiknya dihindari. Cari alternatif bersertifikat halal dan komposisi yang lebih sederhana.'
                : ($status === 'syubhat'
                    ? 'Cek sertifikasi halal atau hubungi produsen untuk memastikan sumber bahan kritis.'
                    : 'Secara umum tidak ada indikasi bahan haram dari pencocokan cepat, namun tetap cek label resmi.'),
            'ingredients' => $ingredients,
            'nutrition_estimate' => $nutritionEstimate,
            'health_risk' => $healthRisk,
            'watchouts' => $personalWarnings,
            'bahan_syubhat' => collect($flags)
                ->filter(fn ($flag) => in_array($flag['status_halal'], ['syubhat', 'haram'], true))
                ->pluck('name')
                ->values()
                ->all(),
        ];
    }

    private function fallbackSkincareAnalysis(string $text, array $userContext = []): array
    {
        $normalized = Str::lower($text);
        $riskItems = [];

        foreach ($this->dangerousSkincareKeywords as $keyword) {
            if (Str::contains($normalized, $keyword)) {
                $riskItems[] = [
                    'name' => $keyword,
                    'risk_level' => 'danger',
                    'reason' => 'Bahan ini masuk kategori risiko tinggi untuk keamanan kosmetik.',
                ];
            }
        }

        $halalFlags = $this->detectIngredientFlags($normalized);
        $statusHalal = $this->inferHalalStatusFromText($normalized);
        $statusKeamanan = !empty($riskItems) ? 'bahaya' : (count($halalFlags) > 0 ? 'perhatian' : 'aman');
        $score = !empty($riskItems) ? 20 : ($statusHalal === 'syubhat' ? 68 : ($statusHalal === 'haram' ? 15 : 88));

        $personalWarnings = $this->buildPersonalWarnings($normalized, $userContext);
        foreach ($personalWarnings as $warning) {
            $riskItems[] = [
                'name' => 'profil_pribadi',
                'risk_level' => 'warning',
                'reason' => $warning,
            ];
        }

        return [
            'status_keamanan' => $statusKeamanan,
            'skor_keamanan' => $score,
            'status_halal' => $statusHalal,
            'ringkasan' => $statusKeamanan === 'bahaya'
                ? 'Terdeteksi bahan yang perlu diwaspadai pada produk kosmetik ini.'
                : 'Analisis cepat tidak menemukan indikasi bahaya besar, namun tetap pastikan izin edar dan label lengkap.',
            'bahan_berisiko' => $riskItems,
            'bahan_syubhat' => collect($halalFlags)
                ->filter(fn ($item) => $item['status_halal'] !== 'halal')
                ->pluck('name')
                ->values()
                ->all(),
            'rekomendasi' => [
                'Gunakan patch test sebelum pemakaian rutin.',
                'Cek nomor notifikasi BPOM dan status halal resmi bila tersedia.',
                'Hentikan pemakaian bila muncul iritasi berat.',
            ],
            'disclaimer' => 'Analisis ini bersifat edukatif dan tidak menggantikan konsultasi dokter kulit atau apoteker.',
        ];
    }

    private function fallbackComparison(array $productsData, array $userContext = []): array
    {
        $products = collect($productsData)->map(function (array $product) use ($userContext) {
            $name = (string) data_get($product, 'name', 'Produk');
            $ingredients = (string) data_get($product, 'ingredients_text', '');
            $statusHalal = Str::lower((string) data_get($product, 'status_halal', 'unknown'));
            $halalScore = match ($statusHalal) {
                'halal' => 100,
                'syubhat' => 58,
                'haram' => 12,
                default => 72,
            };

            $nutritionScore = $this->estimateNutritionScore($product, $ingredients);
            $safetyScore = $this->estimateSafetyScore($ingredients);
            $profileScore = $this->estimateProfileFitScore($product, $ingredients, $userContext);
            $overallScore = (int) round(($halalScore * 0.35) + ($nutritionScore * 0.25) + ($safetyScore * 0.2) + ($profileScore * 0.2));

            $warnings = [];
            if ($statusHalal === 'haram') {
                $warnings[] = 'Ada indikasi bahan haram.';
            } elseif ($statusHalal === 'syubhat') {
                $warnings[] = 'Ada bahan dengan titik kritis halal.';
            }
            if ($profileScore < 60) {
                $warnings[] = 'Kurang cocok dengan profil kesehatan pengguna.';
            }

            $strengths = [];
            if ($halalScore >= 90) {
                $strengths[] = 'Status halal paling aman di antara kandidat.';
            }
            if ($nutritionScore >= 80) {
                $strengths[] = 'Profil nutrisi relatif lebih baik.';
            }
            if ($safetyScore >= 80) {
                $strengths[] = 'Komposisi terlihat lebih sederhana dan minim indikator risiko.';
            }

            return [
                'name' => $name,
                'halal_score' => $halalScore,
                'nutrition_score' => $nutritionScore,
                'safety_score' => $safetyScore,
                'profile_score' => $profileScore,
                'overall_score' => $overallScore,
                'strengths' => $strengths,
                'warnings' => $warnings,
            ];
        })->sortByDesc('overall_score')->values();

        $winner = $products->first();

        return [
            'summary' => 'Perbandingan memperhitungkan status halal, nutrisi, keamanan, dan kecocokan profil pribadi.',
            'recommendation' => $winner
                ? "{$winner['name']} lebih direkomendasikan karena skornya paling stabil di aspek halal, keamanan, dan kecocokan profil."
                : 'Belum ada produk yang bisa dibandingkan.',
            'winner' => $winner
                ? ['name' => $winner['name'], 'reason' => 'Skor total paling tinggi pada perbandingan saat ini.']
                : ['name' => null, 'reason' => ''],
            'products' => $products->all(),
        ];
    }

    private function fallbackSymptomsAnalysis(string $symptoms, array $userContext = []): array
    {
        $normalized = Str::lower($symptoms);
        $profileNotes = $this->buildPersonalWarnings($normalized, $userContext);
        $cluster = 'generic';

        if (Str::contains($normalized, ['perut', 'maag', 'lambung', 'mual', 'pedas', 'diare', 'ulu hati'])) {
            $cluster = 'gastric';
        } elseif (Str::contains($normalized, ['pusing', 'kepala', 'tengkuk', 'vertigo', 'darah tinggi', 'hipertensi'])) {
            $cluster = 'headache';
        } elseif (Str::contains($normalized, ['batuk', 'pilek', 'demam', 'flu', 'tenggorokan'])) {
            $cluster = 'flu';
        } elseif (Str::contains($normalized, ['terkilir', 'keseleo', 'jatuh', 'kecelakaan', 'patah', 'cedera', 'bengkak', 'memar', 'motor', 'terpeleset', 'benturan', 'tertabrak', 'kecelakaan', 'sprain', 'fraktur'])) {
            $cluster = 'injury';
        } elseif (Str::contains($normalized, ['luka', 'berdarah', 'lecet', 'sayat', 'tergores', 'robek', 'sobek', 'tertusuk', 'terpotong'])) {
            $cluster = 'skin_wound';
        } elseif (Str::contains($normalized, ['pegal', 'kram', 'otot', 'punggung', 'pinggang', 'leher kaku', 'nyeri otot', 'badan sakit', 'encok', 'rematik', 'sendi'])) {
            $cluster = 'muscle_pain';
        } elseif (Str::contains($normalized, ['gatal', 'ruam', 'bentol', 'alergi'])) {
            $cluster = 'allergy';
        }

        $severity = 'mild';
        $severityLabel = 'Ringan';
        $severityReason = 'Belum tampak kata kunci gawat darurat pada keluhan yang disampaikan.';
        $emergencyWarning = 'Segera ke IGD jika muncul sesak napas, pingsan, kejang, muntah darah, BAB hitam, nyeri dada hebat, atau penurunan kesadaran.';

        if (Str::contains($normalized, ['sesak napas', 'pingsan', 'kejang', 'muntah darah', 'bab hitam', 'nyeri dada', 'patah tulang', 'tulang menonjol', 'tidak bisa digerakkan', 'kelumpuhan'])) {
            $severity = 'emergency';
            $severityLabel = 'Perlu perhatian medis';
            $severityReason = 'Terdapat gejala red flag yang memerlukan evaluasi medis segera.';
        } elseif (Str::contains($normalized, ['demam tinggi', 'lebih dari 2 hari', 'muntah terus', 'dehidrasi', 'sangat lemas', 'kecelakaan', 'terkilir', 'bengkak parah', 'memar luas', 'jatuh dari motor', 'jatuh dari ketinggian'])) {
            $severity = 'moderate';
            $severityLabel = 'Sedang';
            $severityReason = 'Keluhan berpotensi memerlukan pemeriksaan dokter bila tidak membaik dalam waktu singkat.';
        }

        $library = $this->symptomLibrary($cluster);
        $medicines = $library['medicines'];
        $dosageText = collect($medicines)
            ->map(fn ($med) => "{$med['name']}: {$med['dosage']}. {$med['how_to_take']} ({$med['duration']}).")
            ->implode(' ');

        return [
            'ringkasan_keluhan' => $library['summary_prefix'] . $symptoms . '.',
            'severity' => $severity,
            'tingkat_keparahan_label' => $severityLabel,
            'alasan_keparahan' => $severityReason,
            'condition' => $library['condition'],
            'possible_causes' => $library['possible_causes'],
            'gejala_terkait' => $library['related_symptoms'],
            'disease_explanations' => $library['diseases'],
            'trigger_factors' => array_values(array_unique(array_merge($library['triggers'], $profileNotes))),
            'recommended_ingredients' => $library['active_ingredients'],
            'recommended_medicines_list' => $medicines,
            'dosage_guidelines' => $dosageText,
            'drug_mechanism' => $library['drug_mechanism'],
            'halal_check' => [
                'status' => 'Perlu cek label',
                'notes' => 'Pilih obat yang memiliki informasi komposisi jelas, izin edar, dan bila tersedia sertifikasi halal. Untuk kapsul/gel lunak, cek sumber gelatin.',
            ],
            'usage_instructions' => $library['usage_instructions'],
            'lifestyle_advice' => $library['diet_advice'],
            'first_aid_steps' => $library['first_aid'],
            'prevention' => $library['prevention'],
            'emergency_warning' => $emergencyWarning,
            'follow_up_questions' => $library['follow_up_questions'],
            'confidence_level' => $severity === 'emergency' ? 'Sedang' : 'Tinggi',
            'recommendation' => $library['recommendation'],
            'tldr' => $library['tldr'],
        ];
    }

    private function fallbackDrugInteraction(array $drugA, array $drugB): array
    {
        $nameA = Str::lower((string) ($drugA['name'] ?? $drugA['generic_name'] ?? 'obat a'));
        $nameB = Str::lower((string) ($drugB['name'] ?? $drugB['generic_name'] ?? 'obat b'));
        $pair = collect([$nameA, $nameB])->sort()->implode('|');

        $majorPairs = [
            'aspirin|ibuprofen' => 'Keduanya dapat meningkatkan risiko iritasi lambung dan perdarahan.',
            'clopidogrel|omeprazole' => 'Omeprazole dapat menurunkan aktivasi clopidogrel pada sebagian pasien.',
            'ibuprofen|warfarin' => 'Risiko perdarahan dapat meningkat.',
        ];

        if (isset($majorPairs[$pair])) {
            return [
                'has_interaction' => true,
                'severity' => 'major',
                'description' => $majorPairs[$pair],
                'recommendation' => 'Jangan kombinasikan tanpa arahan dokter/apoteker. Pertimbangkan alternatif yang lebih aman.',
                'scientific_basis' => 'Interaksi obat dikenal secara umum dalam praktik klinis.',
                'sources' => ['fallback_clinical_rules'],
                'disclaimer' => 'Ini adalah panduan edukatif dan bukan pengganti keputusan klinis profesional.',
            ];
        }

        if ($nameA === $nameB) {
            return [
                'has_interaction' => true,
                'severity' => 'moderate',
                'description' => 'Ada risiko duplikasi terapi karena nama obat tampak sama atau sangat mirip.',
                'recommendation' => 'Cek ulang kandungan aktif dan hindari konsumsi ganda tanpa arahan tenaga kesehatan.',
                'scientific_basis' => 'Duplikasi dosis dapat meningkatkan risiko efek samping.',
                'sources' => ['fallback_duplicate_check'],
                'disclaimer' => 'Periksa etiket obat dan konsultasikan bila ragu.',
            ];
        }

        return [
            'has_interaction' => false,
            'severity' => 'minor',
            'description' => 'Tidak ditemukan pola interaksi kuat dari pemeriksaan cepat.',
            'recommendation' => 'Tetap konsumsi sesuai aturan pakai dan pantau bila muncul keluhan baru.',
            'scientific_basis' => 'Pencocokan cepat tidak menemukan pasangan interaksi mayor yang umum.',
            'sources' => ['fallback_clinical_rules'],
            'disclaimer' => 'Cek kembali dengan dokter/apoteker bila obat diminum rutin atau memiliki penyakit penyerta.',
        ];
    }

    private function fallbackMedicineAlternative(array $medicine): array
    {
        $name = (string) ($medicine['name'] ?? $medicine['generic_name'] ?? 'Obat');
        $generic = (string) ($medicine['generic_name'] ?? $name);
        $status = $this->inferHalalStatusFromText(Str::lower($name . ' ' . ($medicine['ingredients'] ?? '')));

        return [
            'original_medicine' => $name,
            'status' => $status,
            'reason' => $status === 'halal'
                ? 'Obat ini tidak menunjukkan indikator bahan haram dari pencocokan cepat.'
                : 'Perlu alternatif karena ada titik kritis halal atau informasi bahan belum cukup jelas.',
            'alternatives' => [
                [
                    'name' => "Minta versi {$generic} tablet/kaplet dari produsen dengan komposisi kapsul non-gelatin",
                    'reason' => 'Bahan aktif tetap sama tetapi bentuk sediaan bisa lebih aman dari sisi titik kritis halal.',
                    'notes' => 'Verifikasi izin edar dan komposisi eksipien pada kemasan.',
                ],
                [
                    'name' => 'Konsultasikan obat generik ekuivalen di apotek/rumah sakit',
                    'reason' => 'Apoteker dapat memilih merek dengan eksipien dan kapsul yang lebih jelas sumbernya.',
                    'notes' => 'Jangan mengganti obat resep tanpa persetujuan dokter.',
                ],
            ],
            'overall_note' => 'Fokuskan pencarian alternatif pada bahan aktif yang sama, bentuk sediaan tablet, dan produsen dengan informasi komposisi lengkap.',
        ];
    }

    private function fallbackProductAlternative(string $productName, string $ingredients = '', string $category = ''): array
    {
        $status = $this->inferHalalStatusFromText(Str::lower($ingredients . ' ' . $productName));

        return [
            'original_product' => $productName,
            'status' => $status,
            'reason' => $status === 'halal'
                ? 'Produk ini tidak menunjukkan indikator bahan haram dari pencocokan cepat.'
                : 'Ada titik kritis halal atau komposisi terlalu ambigu untuk dinyatakan aman penuh.',
            'alternatives' => [
                [
                    'name' => "Produk {$category} dengan sertifikasi halal resmi",
                    'reason' => 'Sertifikasi halal resmi memberi kepastian lebih tinggi dibanding analisis cepat semata.',
                    'notes' => 'Bandingkan juga gula, garam, dan bahan tambahan pada label.',
                ],
                [
                    'name' => 'Pilihan lokal dengan komposisi sederhana dan bahan mudah dikenali',
                    'reason' => 'Semakin sederhana komposisi, semakin mudah diverifikasi dari sisi halal dan kesehatan.',
                    'notes' => 'Utamakan produsen yang transparan soal bahan dan izin edar.',
                ],
            ],
            'overall_note' => 'Alternatif terbaik adalah produk dengan label komposisi jelas, izin edar aktif, dan sertifikasi halal resmi bila tersedia.',
        ];
    }

    private function fallbackLabAnalysis(array $manualData = []): array
    {
        $detectedTests = [];

        foreach ($manualData as $key => $value) {
            if (is_array($value)) {
                $testName = (string) ($value['test_name'] ?? $value['name'] ?? Str::headline((string) $key));
                $numericValue = (float) ($value['value'] ?? 0);
                $unit = (string) ($value['unit'] ?? '');
            } else {
                $testName = Str::headline((string) $key);
                $numericValue = is_numeric($value) ? (float) $value : 0;
                $unit = '';
            }

            $detectedTests[] = [
                'test_name' => $testName,
                'value' => $numericValue,
                'unit' => $unit,
                'reference_range' => $this->referenceRangeForTest($testName),
                'status' => $this->statusForLabValue($testName, $numericValue),
                'interpretation' => $this->interpretLabValue($testName, $numericValue),
            ];
        }

        $urgency = collect($detectedTests)->contains(fn ($item) => ($item['status'] ?? '') === 'Tinggi')
            ? 'Ya'
            : 'Tidak';

        return [
            'ringkasan_kondisi' => empty($detectedTests)
                ? 'Belum ada parameter lab yang dapat dipastikan. Isi data manual atau unggah foto yang lebih jelas.'
                : 'Beberapa parameter berhasil dibaca. Fokus utama ada pada nilai yang berada di luar kisaran rujukan umum.',
            'saran_gaya_hidup' => 'Diskusikan hasil dengan dokter terutama bila ada gejala, penyakit penyerta, atau nilai jauh di luar batas normal.',
            'urgensi' => $urgency,
            'detected_tests' => $detectedTests,
            'poin_perhatian' => collect($detectedTests)
                ->map(fn ($item) => [
                    'parameter' => $item['test_name'],
                    'nilai' => $item['value'],
                    'rujukan' => $item['reference_range'],
                    'status' => $item['status'],
                    'penjelasan' => $item['interpretation'],
                ])
                ->values()
                ->all(),
        ];
    }

    private function fallbackFoodAnalysis(): array
    {
        return [
            'food_items' => [
                [
                    'name' => 'Nasi putih',
                    'weight_gram' => 150,
                    'calories' => 195,
                    'carbs' => 42,
                    'protein' => 3.8,
                    'fat' => 0.4,
                    'is_halal' => true,
                    'halal_note' => 'Aman bila tidak dicampur bahan olahan yang meragukan.',
                ],
                [
                    'name' => 'Ayam panggang',
                    'weight_gram' => 100,
                    'calories' => 185,
                    'carbs' => 0,
                    'protein' => 27,
                    'fat' => 8,
                    'is_halal' => true,
                    'halal_note' => 'Perlu cek bumbu marinasi bila produk kemasan atau restoran non-halal.',
                ],
                [
                    'name' => 'Sayur rebus',
                    'weight_gram' => 80,
                    'calories' => 35,
                    'carbs' => 6,
                    'protein' => 2,
                    'fat' => 0.5,
                    'is_halal' => true,
                    'halal_note' => 'Umumnya aman.',
                ],
            ],
            'total_calories' => 415,
            'total_carbs' => 48,
            'total_protein' => 32.8,
            'total_fat' => 8.9,
            'analysis_note' => 'Estimasi fallback digunakan. Untuk hasil lebih akurat, unggah foto dengan pencahayaan baik dan posisi lebih dekat.',
        ];
    }

    private function fallbackSubstitution(array $ingredients, string $context = ''): array
    {
        $substitutions = collect($ingredients)->map(function ($ingredient) {
            $name = Str::lower((string) $ingredient);
            $status = $this->inferHalalStatusFromText($name);

            $halalSubstitute = null;
            $healthySubstitute = null;
            $note = 'Gunakan takaran bertahap lalu sesuaikan rasa.';

            if (Str::contains($name, ['gelatin'])) {
                $halalSubstitute = 'agar-agar atau pektin';
                $healthySubstitute = 'agar-agar';
            } elseif (Str::contains($name, ['lard', 'babi'])) {
                $halalSubstitute = 'minyak nabati atau mentega halal';
                $healthySubstitute = 'minyak zaitun atau canola';
            } elseif (Str::contains($name, ['mirin', 'wine', 'rum', 'sake'])) {
                $halalSubstitute = 'kaldu jamur, air jeruk, atau cuka apel secukupnya';
                $healthySubstitute = 'kaldu rendah sodium';
            } elseif (Str::contains($name, ['collagen'])) {
                $halalSubstitute = 'kolagen halal bersertifikat atau agar';
                $healthySubstitute = 'agar atau chia gel';
            } elseif (Str::contains($name, ['sugar', 'gula'])) {
                $status = 'halal';
                $healthySubstitute = 'gula kelapa, kurma, atau kurangi porsi';
            }

            return [
                'original' => (string) $ingredient,
                'status' => $status,
                'reason' => $this->halalReason($status),
                'halal_substitute' => $halalSubstitute,
                'healthy_substitute' => $healthySubstitute,
                'substitute_note' => $note,
            ];
        })->values()->all();

        return [
            'ingredients' => $substitutions,
            'overall_note' => $context !== ''
                ? "Resep {$context} tetap bisa dibuat dengan penggantian bahan bertahap agar rasa dan teksturnya tetap seimbang."
                : 'Gunakan substitusi halal yang mendekati fungsi bahan asli lalu sesuaikan rasa secara bertahap.',
        ];
    }

    private function detectIngredientFlags(string $normalizedText): array
    {
        $flags = [];

        foreach ($this->haramKeywords as $keyword) {
            if (Str::contains($normalizedText, $keyword)) {
                $flags[$keyword] = [
                    'name' => $keyword,
                    'status_halal' => 'haram',
                    'risk_level' => 'high',
                    'note' => 'Terindikasi bahan haram atau turunannya.',
                ];
            }
        }

        foreach ($this->syubhatKeywords as $keyword) {
            if (Str::contains($normalizedText, $keyword) && !isset($flags[$keyword])) {
                $flags[$keyword] = [
                    'name' => $keyword,
                    'status_halal' => 'syubhat',
                    'risk_level' => 'medium',
                    'note' => 'Perlu verifikasi sumber bahan atau sertifikasi halal.',
                ];
            }
        }

        return array_values($flags);
    }

    private function buildPersonalWarnings(string $normalizedText, array $userContext = []): array
    {
        $warnings = [];
        $allergyText = Str::lower((string) ($userContext['allergies'] ?? $userContext['allergy'] ?? ''));
        $medicalHistory = Str::lower((string) ($userContext['medical_history'] ?? ''));

        $allergens = collect(preg_split('/[,;|]/', $allergyText))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        foreach ($allergens as $allergen) {
            if (Str::contains($normalizedText, $allergen)) {
                $warnings[] = "Terdeteksi alergen pribadi: {$allergen}.";
            }
        }

        if (($userContext['diabetes'] ?? false) || Str::contains($medicalHistory, 'diabet')) {
            if (Str::contains($normalizedText, ['gula', 'sugar', 'glucose', 'fructose', 'sirup'])) {
                $warnings[] = 'Ada indikator bahan tinggi gula yang perlu dibatasi untuk profil diabetes.';
            }
        }

        if (Str::contains($medicalHistory, ['hipertensi', 'darah tinggi'])) {
            if (Str::contains($normalizedText, ['garam', 'salt', 'sodium', 'msg', 'monosodium'])) {
                $warnings[] = 'Ada indikator sodium tinggi yang perlu dibatasi untuk profil hipertensi.';
            }
        }

        if (Str::contains($medicalHistory, ['kolesterol', 'jantung'])) {
            if (Str::contains($normalizedText, ['lemak', 'lard', 'cream', 'krimer', 'santan'])) {
                $warnings[] = 'Ada bahan berlemak yang sebaiknya dibatasi untuk profil kardiometabolik.';
            }
        }

        return array_values(array_unique($warnings));
    }

    private function inferHalalStatusFromText(string $text): string
    {
        if (Str::contains($text, $this->haramKeywords)) {
            return 'haram';
        }

        if (Str::contains($text, $this->syubhatKeywords)) {
            return 'syubhat';
        }

        return trim($text) === '' ? 'unknown' : 'halal';
    }

    private function halalReason(string $status): string
    {
        return match ($status) {
            'haram' => 'Ditemukan indikator bahan haram atau turunannya yang perlu dihindari.',
            'syubhat' => 'Ada bahan dengan titik kritis halal sehingga perlu verifikasi sumber atau sertifikasi.',
            'halal' => 'Tidak ditemukan indikator bahan haram dari pencocokan cepat, namun tetap cek label resmi.',
            default => 'Informasi bahan belum cukup untuk menentukan status halal secara meyakinkan.',
        };
    }

    private function estimateNutritionFromIngredients(string $normalizedText): array
    {
        $sugar = 0;
        $sodium = 120;
        $calories = 140;

        if (Str::contains($normalizedText, ['gula', 'sugar', 'sirup', 'glucose', 'fructose'])) {
            $sugar += 18;
            $calories += 70;
        }

        if (Str::contains($normalizedText, ['garam', 'salt', 'sodium', 'msg', 'monosodium'])) {
            $sodium += 500;
        }

        if (Str::contains($normalizedText, ['cream', 'krimer', 'santan', 'butter', 'mentega', 'minyak'])) {
            $calories += 90;
        }

        return [
            'sugar_g' => $sugar,
            'sodium_mg' => $sodium,
            'calories' => $calories,
        ];
    }

    private function estimateNutritionScore(array $product, string $ingredients): int
    {
        $score = 78;
        $statusHalal = Str::lower((string) data_get($product, 'status_halal', 'unknown'));
        $sugars = (float) (data_get($product, 'nutriments.sugars_100g')
            ?? data_get($product, 'sugar_g')
            ?? 0);

        if ($sugars > 15) {
            $score -= 20;
        } elseif ($sugars > 8) {
            $score -= 10;
        }

        if (Str::contains(Str::lower($ingredients), ['gula', 'sirup', 'fructose'])) {
            $score -= 8;
        }

        if ($statusHalal === 'haram') {
            $score -= 25;
        }

        return max(15, min(98, $score));
    }

    private function estimateSafetyScore(string $ingredients): int
    {
        $score = 82;
        if (Str::contains($ingredients, $this->haramKeywords)) {
            $score -= 35;
        }
        if (Str::contains($ingredients, $this->syubhatKeywords)) {
            $score -= 12;
        }
        if (Str::contains($ingredients, $this->dangerousSkincareKeywords)) {
            $score -= 45;
        }

        return max(10, min(98, $score));
    }

    private function estimateProfileFitScore(array $product, string $ingredients, array $userContext): int
    {
        $score = 84;
        $warnings = $this->buildPersonalWarnings(Str::lower($ingredients), $userContext);
        $score -= count($warnings) * 15;

        return max(10, min(98, $score));
    }

    private function scoreFromHalalStatus(string $status): int
    {
        return match ($status) {
            'halal' => 90,
            'syubhat' => 62,
            'haram' => 18,
            default => 70,
        };
    }

    private function healthRiskToSafetyStatus(string $healthRisk): string
    {
        return match ($healthRisk) {
            'high' => 'bahaya',
            'moderate' => 'perhatian',
            default => 'aman',
        };
    }

    private function symptomLibrary(string $cluster): array
    {
        return match ($cluster) {
            'gastric' => [
                'summary_prefix' => 'Kamu menyampaikan keluhan yang paling mengarah ke gangguan lambung atau pencernaan setelah pemicu makanan/minuman. Keluhan utama yang saya tangkap: ',
                'condition' => 'Iritasi lambung / gastritis ringan',
                'possible_causes' => [
                    ['name' => 'Iritasi lambung', 'percentage' => 60, 'reason' => 'Keluhan berhubungan dengan pedas, mual, nyeri ulu hati, atau perut tidak nyaman.'],
                    ['name' => 'Asam lambung meningkat', 'percentage' => 25, 'reason' => 'Gejala seperti mual, perih, kembung, atau rasa panas sering terkait peningkatan asam lambung.'],
                    ['name' => 'Gangguan pencernaan fungsional', 'percentage' => 15, 'reason' => 'Bisa dipicu pola makan tidak teratur, stres, atau kombinasi makanan tertentu.'],
                ],
                'related_symptoms' => ['mual', 'perih ulu hati', 'kembung', 'sendawa', 'nafsu makan menurun'],
                'diseases' => [
                    [
                        'name' => 'Gastritis',
                        'description' => 'Gastritis adalah peradangan pada dinding lambung yang bisa muncul akibat makanan terlalu pedas, asam, telat makan, obat anti nyeri tertentu, atau infeksi.',
                        'relation_to_case' => 'Keluhan kamu paling cocok dengan pola iritasi lambung ringan hingga sedang.',
                    ],
                    [
                        'name' => 'Dispepsia',
                        'description' => 'Dispepsia adalah kumpulan gejala seperti begah, cepat kenyang, mual, dan rasa tidak nyaman di perut atas.',
                        'relation_to_case' => 'Kondisi ini sering muncul setelah pola makan yang memicu lambung.',
                    ],
                ],
                'triggers' => ['Makanan pedas atau terlalu asam', 'Makan saat perut kosong', 'Porsi terlalu besar', 'Kurang minum atau kurang istirahat'],
                'active_ingredients' => ['antasida', 'omeprazole', 'simethicone'],
                'medicines' => [
                    [
                        'name' => 'Antasida',
                        'function' => 'Menetralkan asam lambung dengan cepat untuk meredakan rasa perih atau panas.',
                        'dosage' => 'Ikuti dosis pada kemasan, umumnya digunakan 1-2 tablet kunyah atau 5-10 mL saat gejala muncul',
                        'how_to_take' => 'Gunakan setelah makan atau saat keluhan kambuh sesuai petunjuk kemasan',
                        'duration' => '1-3 hari untuk keluhan ringan; bila berulang perlu evaluasi dokter',
                        'when_to_take' => 'Sesudah makan atau saat gejala timbul',
                        'halal_status' => 'Perlu cek label/kemasan',
                        'safety_note' => 'Bacalah komposisi dan hindari penggunaan berlebihan, terutama bila ada penyakit ginjal.',
                        'side_effects' => ['kembung ringan', 'sembelit atau diare pada sebagian orang'],
                    ],
                    [
                        'name' => 'Omeprazole',
                        'function' => 'Mengurangi produksi asam lambung sehingga membantu keluhan yang berulang.',
                        'dosage' => 'Gunakan hanya sesuai anjuran dokter atau petunjuk kemasan resmi',
                        'how_to_take' => 'Diminum sebelum makan, biasanya pagi hari, bila memang direkomendasikan tenaga kesehatan',
                        'duration' => 'Biasanya beberapa hari sampai evaluasi, jangan digunakan sembarangan jangka panjang',
                        'when_to_take' => 'Sebelum makan',
                        'halal_status' => 'Perlu cek produsen dan bahan kapsul',
                        'safety_note' => 'Lebih aman bila digunakan setelah konsultasi, terutama jika gejala sering kambuh.',
                        'side_effects' => ['mual ringan', 'sakit kepala', 'kembung'],
                    ],
                ],
                'drug_mechanism' => 'Antasida bekerja dengan menetralkan asam lambung secara langsung, sedangkan omeprazole menurunkan produksi asam dari sumbernya.',
                'usage_instructions' => 'Utamakan obat bebas yang sesuai label. Bila keluhan sering berulang, jangan hanya mengandalkan obat dan segera evaluasi penyebab dasarnya.',
                'diet_advice' => 'Pilih makanan lembut seperti nasi, roti tawar, pisang, sup bening, dan air hangat. Hindari pedas, kopi, soda, alkohol, serta gorengan berat.',
                'first_aid' => ['Minum air hangat sedikit demi sedikit', 'Duduk tegak 15-30 menit', 'Hindari rebahan setelah makan', 'Konsumsi makanan ringan yang tidak pedas'],
                'prevention' => ['Makan lebih teratur', 'Batasi makanan sangat pedas atau asam', 'Jangan langsung tidur setelah makan', 'Kenali makanan pemicu pribadi'],
                'follow_up_questions' => ['Apakah nyerinya dominan di ulu hati?', 'Apakah ada muntah, BAB hitam, atau demam tinggi?', 'Apakah keluhan sering muncul setelah makanan tertentu?'],
                'recommendation' => 'Keluhan paling mungkin terkait iritasi lambung ringan. Fokuskan pada istirahat pencernaan, perbaikan pola makan, dan obat lambung yang sesuai label bila perlu.',
                'tldr' => 'Kemungkinan terbesar adalah iritasi lambung akibat pola makan/pemicu tertentu. Biasanya membaik dengan istirahat, diet lembut, dan obat lambung yang tepat.',
            ],
            'headache' => [
                'summary_prefix' => 'Keluhan yang kamu sampaikan paling mengarah ke nyeri kepala, peningkatan tekanan darah, atau gangguan vestibular ringan. Keluhan yang saya tangkap: ',
                'condition' => 'Nyeri kepala tegang / peningkatan tekanan darah yang perlu dipantau',
                'possible_causes' => [
                    ['name' => 'Nyeri kepala tegang', 'percentage' => 45, 'reason' => 'Sering muncul saat kurang tidur, stres, atau kelelahan.'],
                    ['name' => 'Tekanan darah meningkat', 'percentage' => 35, 'reason' => 'Keluhan pusing, tengkuk tegang, dan riwayat makan tinggi garam/lemak bisa mengarah ke sini.'],
                    ['name' => 'Vertigo ringan atau gangguan keseimbangan', 'percentage' => 20, 'reason' => 'Bila ada sensasi berputar atau mual yang dominan.'],
                ],
                'related_symptoms' => ['pusing', 'nyeri kepala', 'tengkuk kaku', 'mual', 'sensasi berputar'],
                'diseases' => [
                    [
                        'name' => 'Hipertensi',
                        'description' => 'Hipertensi adalah kondisi tekanan darah berada di atas batas normal dan pada sebagian orang dapat memicu pusing, berat di kepala, atau nyeri tengkuk.',
                        'relation_to_case' => 'Perlu dipertimbangkan bila ada riwayat darah tinggi, konsumsi garam tinggi, atau gejala berulang.',
                    ],
                    [
                        'name' => 'Tension headache',
                        'description' => 'Nyeri kepala tegang biasanya terasa seperti kepala diikat, dipicu stres, kurang tidur, dan postur yang kurang baik.',
                        'relation_to_case' => 'Ini sangat umum dan sering membaik dengan istirahat, hidrasi, dan kontrol pemicu.',
                    ],
                ],
                'triggers' => ['Kurang tidur', 'Stres atau tegang', 'Asupan garam/lemak tinggi', 'Kurang minum'],
                'active_ingredients' => ['paracetamol', 'oralit', 'amlodipine (perlu resep dokter bila hipertensi)'],
                'medicines' => [
                    [
                        'name' => 'Paracetamol',
                        'function' => 'Membantu meredakan nyeri kepala ringan hingga sedang.',
                        'dosage' => 'Ikuti dosis pada kemasan dan jangan melebihi dosis harian maksimum',
                        'how_to_take' => 'Diminum sesuai label, lebih aman setelah makan bila lambung sensitif',
                        'duration' => '1-2 hari untuk keluhan akut; jika berulang perlu evaluasi',
                        'when_to_take' => 'Saat nyeri muncul',
                        'halal_status' => 'Mayoritas tablet generik relatif aman, tetap cek label',
                        'safety_note' => 'Hindari penggunaan berlebihan, terutama bila ada penyakit hati.',
                        'side_effects' => ['mual ringan', 'reaksi alergi sangat jarang'],
                    ],
                    [
                        'name' => 'Amlodipine',
                        'function' => 'Obat resep untuk membantu menurunkan tekanan darah pada pasien hipertensi.',
                        'dosage' => 'Harus sesuai resep dokter',
                        'how_to_take' => 'Diminum sesuai jadwal yang ditetapkan dokter',
                        'duration' => 'Digunakan sesuai evaluasi dokter',
                        'when_to_take' => 'Biasanya waktu yang sama setiap hari',
                        'halal_status' => 'Perlu cek produsen dan izin edar',
                        'safety_note' => 'Jangan memulai sendiri tanpa pengukuran tekanan darah dan konsultasi.',
                        'side_effects' => ['pusing', 'bengkak kaki ringan', 'rasa hangat pada wajah'],
                    ],
                ],
                'drug_mechanism' => 'Paracetamol membantu mengurangi persepsi nyeri, sedangkan obat antihipertensi bekerja dengan membantu menurunkan tekanan pada pembuluh darah.',
                'usage_instructions' => 'Bila curiga tekanan darah tinggi, sebaiknya ukur tekanan darah terlebih dahulu. Jangan mengandalkan obat nyeri terus-menerus tanpa mencari penyebab.',
                'diet_advice' => 'Perbanyak air putih, buah kaya kalium seperti pisang, dan makanan rendah garam. Batasi kopi berlebihan, santan pekat, gorengan, dan makanan terlalu asin.',
                'first_aid' => ['Duduk atau berbaring di tempat tenang', 'Minum air putih', 'Longgarkan pakaian yang ketat', 'Hindari aktivitas berat dulu'],
                'prevention' => ['Tidur cukup', 'Kelola stres', 'Kurangi garam harian', 'Cek tekanan darah secara berkala'],
                'follow_up_questions' => ['Apakah pusing disertai penglihatan kabur?', 'Apakah ada riwayat hipertensi di keluarga?', 'Apakah keluhan muncul setelah makanan tertentu atau kurang tidur?'],
                'recommendation' => 'Pantau tekanan darah bila memungkinkan, istirahat, dan gunakan analgesik umum secara bijak. Jika keluhan berat atau berulang, konsultasi dokter.',
                'tldr' => 'Keluhan kemungkinan terkait nyeri kepala tegang atau tekanan darah yang meningkat. Istirahat, hidrasi, dan pemantauan tekanan darah sangat dianjurkan.',
            ],
            'flu' => [
                'summary_prefix' => 'Keluhan kamu paling mengarah ke infeksi saluran napas atas ringan atau flu biasa. Keluhan utama yang saya tangkap: ',
                'condition' => 'Flu / infeksi saluran napas atas ringan',
                'possible_causes' => [
                    ['name' => 'Flu biasa', 'percentage' => 55, 'reason' => 'Gejala batuk, pilek, tenggorokan tidak nyaman, dan demam ringan paling sering karena flu.'],
                    ['name' => 'Radang tenggorokan ringan', 'percentage' => 25, 'reason' => 'Bila nyeri tenggorokan lebih dominan.'],
                    ['name' => 'Alergi saluran napas', 'percentage' => 20, 'reason' => 'Bila pilek encer, bersin, dan tidak ada demam bermakna.'],
                ],
                'related_symptoms' => ['batuk', 'pilek', 'demam ringan', 'hidung tersumbat', 'nyeri tenggorokan'],
                'diseases' => [
                    [
                        'name' => 'ISPA ringan',
                        'description' => 'Infeksi saluran napas atas ringan umumnya disebabkan virus dan sering membaik dengan istirahat, cairan cukup, dan terapi simptomatik.',
                        'relation_to_case' => 'Pola gejalanya cukup sesuai dengan keluhan flu sehari-hari.',
                    ],
                ],
                'triggers' => ['Kelelahan', 'Kurang tidur', 'Paparan orang sakit', 'Kurang cairan'],
                'active_ingredients' => ['paracetamol', 'cetirizine', 'dextromethorphan'],
                'medicines' => [
                    [
                        'name' => 'Paracetamol',
                        'function' => 'Membantu menurunkan demam dan meredakan nyeri ringan.',
                        'dosage' => 'Ikuti dosis pada kemasan sesuai usia',
                        'how_to_take' => 'Diminum sesuai aturan pakai, sebaiknya setelah makan bila lambung sensitif',
                        'duration' => 'Digunakan saat perlu selama beberapa hari pertama',
                        'when_to_take' => 'Saat demam atau nyeri muncul',
                        'halal_status' => 'Perlu cek label produsen',
                        'safety_note' => 'Jangan melebihi dosis maksimum harian.',
                        'side_effects' => ['mual ringan', 'reaksi alergi jarang'],
                    ],
                    [
                        'name' => 'Cetirizine',
                        'function' => 'Membantu bila pilek/bersin dipicu komponen alergi.',
                        'dosage' => 'Gunakan sesuai aturan pakai pada kemasan',
                        'how_to_take' => 'Diminum sekali sehari sesuai label',
                        'duration' => 'Sesuai kebutuhan jangka pendek',
                        'when_to_take' => 'Biasanya malam hari bila menimbulkan kantuk',
                        'halal_status' => 'Perlu cek label produsen',
                        'safety_note' => 'Bisa menyebabkan mengantuk pada sebagian orang.',
                        'side_effects' => ['kantuk', 'mulut kering ringan'],
                    ],
                ],
                'drug_mechanism' => 'Obat flu simptomatik umumnya bekerja meredakan gejala seperti demam, bersin, atau batuk; bukan membunuh virus secara langsung.',
                'usage_instructions' => 'Fokus pada istirahat, hidrasi, dan obat simptomatik. Antibiotik tidak digunakan sembarangan tanpa indikasi dokter.',
                'diet_advice' => 'Perbanyak air hangat, sup, buah, dan makanan lunak. Hindari rokok, gorengan berlebihan, dan kurang minum.',
                'first_aid' => ['Istirahat cukup', 'Minum air hangat', 'Gunakan masker bila batuk/pilek', 'Kumur air garam hangat bila tenggorokan tidak nyaman'],
                'prevention' => ['Cuci tangan rutin', 'Tidur cukup', 'Jaga imunitas', 'Kurangi kontak dekat saat sedang flu'],
                'follow_up_questions' => ['Apakah demamnya tinggi atau lebih dari 3 hari?', 'Apakah ada sesak napas atau dahak berdarah?', 'Apakah keluhan lebih dominan batuk atau pilek?'],
                'recommendation' => 'Kemungkinan besar flu ringan. Istirahat, hidrasi, dan obat simptomatik yang sesuai biasanya cukup pada fase awal.',
                'tldr' => 'Keluhan paling sesuai dengan flu/ISPA ringan. Rawat suportif dulu dan waspadai bila demam tinggi atau sesak napas muncul.',
            ],
            'allergy' => [
                'summary_prefix' => 'Keluhan kamu paling mengarah ke reaksi alergi ringan sampai sedang pada kulit atau saluran napas atas. Keluhan utama yang saya tangkap: ',
                'condition' => 'Reaksi alergi ringan',
                'possible_causes' => [
                    ['name' => 'Alergi makanan atau bahan tertentu', 'percentage' => 55, 'reason' => 'Ruam, gatal, atau bentol sering muncul setelah paparan pemicu tertentu.'],
                    ['name' => 'Iritasi kontak', 'percentage' => 25, 'reason' => 'Jika pemicu berasal dari skincare, sabun, atau bahan pada kulit.'],
                    ['name' => 'Reaksi imun non-spesifik', 'percentage' => 20, 'reason' => 'Kadang tubuh bereaksi sementara terhadap bahan baru atau lingkungan.'],
                ],
                'related_symptoms' => ['gatal', 'ruam', 'bentol', 'kulit kemerahan', 'bersin'],
                'diseases' => [
                    [
                        'name' => 'Alergi',
                        'description' => 'Alergi adalah respons imun tubuh yang berlebihan terhadap zat yang dianggap asing, meski bagi orang lain mungkin aman.',
                        'relation_to_case' => 'Keluhan gatal dan bentol sangat khas untuk reaksi alergi ringan.',
                    ],
                ],
                'triggers' => ['Paparan alergen makanan', 'Produk baru pada kulit', 'Debu atau serbuk', 'Riwayat alergi sebelumnya'],
                'active_ingredients' => ['cetirizine', 'loratadine', 'calamine'],
                'medicines' => [
                    [
                        'name' => 'Cetirizine',
                        'function' => 'Membantu meredakan gatal, bentol, atau gejala alergi ringan.',
                        'dosage' => 'Gunakan sesuai aturan pakai pada kemasan',
                        'how_to_take' => 'Diminum sesuai label, biasanya sekali sehari',
                        'duration' => 'Sesuai kebutuhan jangka pendek',
                        'when_to_take' => 'Malam hari jika membuat kantuk',
                        'halal_status' => 'Perlu cek label produsen',
                        'safety_note' => 'Segera ke dokter bila ada bengkak bibir atau sesak napas.',
                        'side_effects' => ['kantuk', 'mulut kering ringan'],
                    ],
                    [
                        'name' => 'Losion calamine',
                        'function' => 'Membantu menenangkan kulit yang gatal ringan.',
                        'dosage' => 'Oles tipis sesuai petunjuk produk',
                        'how_to_take' => 'Untuk pemakaian luar, hindari area luka terbuka',
                        'duration' => 'Beberapa hari atau sampai gejala mereda',
                        'when_to_take' => 'Saat gatal muncul',
                        'halal_status' => 'Perlu cek komposisi dan produsen',
                        'safety_note' => 'Hentikan bila iritasi bertambah.',
                        'side_effects' => ['iritasi ringan lokal pada sebagian orang'],
                    ],
                ],
                'drug_mechanism' => 'Antihistamin membantu menghambat efek histamin yang memicu gatal, bentol, dan bersin pada reaksi alergi.',
                'usage_instructions' => 'Fokus pada penghentian paparan pemicu. Bila ada bengkak bibir, suara serak, atau sesak napas, itu situasi darurat.',
                'diet_advice' => 'Hindari bahan yang dicurigai sebagai pemicu. Pilih makanan sederhana dulu dan catat reaksi tubuh.',
                'first_aid' => ['Hentikan paparan pemicu', 'Bilas area kulit bila pemicu topikal', 'Kompres dingin', 'Pantau apakah muncul sesak napas atau bengkak wajah'],
                'prevention' => ['Kenali pemicu pribadi', 'Baca label produk', 'Uji coba produk baru secara bertahap', 'Simpan daftar alergi pribadi'],
                'follow_up_questions' => ['Apakah ada bengkak bibir atau lidah?', 'Apakah keluhan muncul setelah makanan atau produk tertentu?', 'Apakah kamu punya riwayat alergi sebelumnya?'],
                'recommendation' => 'Kemungkinan besar reaksi alergi ringan. Hentikan pemicu, gunakan antihistamin sesuai label bila perlu, dan waspadai tanda anafilaksis.',
                'tldr' => 'Keluhan paling sesuai dengan reaksi alergi ringan. Hindari pemicu, pantau sesak napas/bengkak, dan gunakan terapi simptomatik yang aman.',
            ],
            'injury' => [
                'summary_prefix' => 'Keluhan kamu paling mengarah ke cedera jaringan lunak akibat trauma fisik seperti jatuh atau benturan. Keluhan utama yang saya tangkap: ',
                'condition' => 'Cedera jaringan lunak / sprain (keseleo/terkilir)',
                'possible_causes' => [
                    ['name' => 'Sprain (keseleo/terkilir)', 'percentage' => 50, 'reason' => 'Cedera pada ligamen akibat gerakan mendadak atau benturan, menyebabkan bengkak dan nyeri di area sendi.'],
                    ['name' => 'Strain (tarikan otot)', 'percentage' => 30, 'reason' => 'Otot atau tendon teregang berlebihan saat jatuh atau menerima tekanan mendadak.'],
                    ['name' => 'Kontusi (memar)', 'percentage' => 20, 'reason' => 'Benturan langsung pada jaringan lunak menyebabkan memar dan pembengkakan lokal.'],
                ],
                'related_symptoms' => ['bengkak', 'nyeri tekan', 'memar', 'keterbatasan gerak', 'kemerahan area cedera', 'nyeri saat digerakkan'],
                'diseases' => [
                    [
                        'name' => 'Sprain (Keseleo)',
                        'description' => 'Sprain adalah cedera pada ligamen — jaringan ikat yang menghubungkan tulang pada sendi. Terjadi saat sendi dipaksa bergerak di luar rentang normal, misalnya saat jatuh, terpeleset, atau kecelakaan.',
                        'relation_to_case' => 'Keluhan terkilir setelah jatuh dari motor sangat khas untuk sprain, terutama di pergelangan tangan, pergelangan kaki, atau lutut.',
                    ],
                    [
                        'name' => 'Strain (Tarikan Otot)',
                        'description' => 'Strain adalah cedera pada otot atau tendon akibat tarikan atau tekanan berlebih. Berbeda dari sprain yang mengenai ligamen, strain mengenai otot dan/atau tendon penghubungnya.',
                        'relation_to_case' => 'Saat jatuh dari motor, tubuh secara refleks menahan benturan sehingga otot bisa teregang melebihi batas normal.',
                    ],
                    [
                        'name' => 'Fraktur ringan (retak tulang)',
                        'description' => 'Fraktur adalah patahnya tulang akibat benturan keras. Fraktur ringan mungkin sulit dibedakan dari sprain berat tanpa rontgen.',
                        'relation_to_case' => 'Jika bengkak sangat besar, nyeri hebat, atau tidak bisa digerakkan sama sekali, kemungkinan fraktur perlu dipertimbangkan dan segera rontgen.',
                    ],
                ],
                'triggers' => ['Jatuh dari motor atau kendaraan', 'Terpeleset atau terjatuh', 'Benturan langsung pada sendi', 'Gerakan mendadak yang memaksa sendi', 'Aktivitas berat tanpa pemanasan'],
                'active_ingredients' => ['ibuprofen', 'paracetamol', 'methyl salicylate'],
                'medicines' => [
                    [
                        'name' => 'Ibuprofen',
                        'function' => 'Obat anti-inflamasi non-steroid (NSAID) yang membantu mengurangi bengkak, nyeri, dan peradangan pada cedera jaringan lunak.',
                        'dosage' => 'Ikuti dosis pada kemasan, umumnya 200-400 mg per dosis untuk dewasa',
                        'how_to_take' => 'Diminum setelah makan untuk mengurangi iritasi lambung',
                        'duration' => '3-5 hari atau sampai bengkak dan nyeri mereda',
                        'when_to_take' => 'Setiap 6-8 jam sesuai kebutuhan',
                        'halal_status' => 'Perlu cek label dan produsen',
                        'safety_note' => 'Hindari jika ada riwayat maag atau gangguan ginjal. Jangan digunakan bersamaan dengan aspirin.',
                        'side_effects' => ['gangguan lambung', 'mual ringan', 'pusing'],
                    ],
                    [
                        'name' => 'Paracetamol',
                        'function' => 'Membantu meredakan nyeri ringan hingga sedang akibat cedera.',
                        'dosage' => 'Ikuti dosis pada kemasan, jangan melebihi dosis harian maksimum',
                        'how_to_take' => 'Diminum sesuai label, boleh saat perut kosong',
                        'duration' => '3-5 hari atau sampai nyeri mereda',
                        'when_to_take' => 'Saat nyeri muncul, setiap 4-6 jam',
                        'halal_status' => 'Mayoritas tablet generik aman, tetap cek label',
                        'safety_note' => 'Jangan melebihi dosis maksimum harian dan hindari alkohol.',
                        'side_effects' => ['mual ringan', 'reaksi alergi sangat jarang'],
                    ],
                    [
                        'name' => 'Salep/Krim Pereda Nyeri (Counterpain/Salonpas/Voltaren Gel)',
                        'function' => 'Pereda nyeri topikal yang membantu meredakan nyeri dan peradangan lokal pada area cedera.',
                        'dosage' => 'Oleskan tipis pada area yang sakit 2-3 kali sehari',
                        'how_to_take' => 'Untuk pemakaian luar saja, hindari area luka terbuka, mata, dan selaput lendir',
                        'duration' => 'Beberapa hari sampai nyeri mereda',
                        'when_to_take' => 'Saat nyeri muncul, bisa dikombinasi dengan obat minum',
                        'halal_status' => 'Perlu cek komposisi dan produsen',
                        'safety_note' => 'Hentikan jika muncul iritasi kulit atau ruam.',
                        'side_effects' => ['rasa panas/dingin lokal', 'iritasi kulit ringan pada sebagian orang'],
                    ],
                ],
                'drug_mechanism' => 'Ibuprofen menghambat enzim COX yang memproduksi prostaglandin (zat penyebab nyeri dan bengkak). Paracetamol bekerja di sistem saraf pusat untuk mengurangi persepsi nyeri. Salep topikal memberikan efek pereda nyeri langsung di area yang dioleskan.',
                'usage_instructions' => 'Prioritaskan metode RICE (Rest, Ice, Compression, Elevation) dalam 48 jam pertama. Obat anti-nyeri membantu mengelola gejala, tetapi bukan pengganti evaluasi medis jika cedera berat.',
                'diet_advice' => 'Perbanyak protein (telur, ikan, ayam) untuk perbaikan jaringan. Konsumsi makanan kaya vitamin C (jeruk, jambu) dan zinc untuk mempercepat penyembuhan. Hindari makanan yang memicu peradangan seperti gorengan berlebihan.',
                'first_aid' => [
                    'Rest — Istirahatkan bagian tubuh yang cedera, jangan dipaksakan bergerak',
                    'Ice — Kompres es yang dibungkus kain selama 15-20 menit, setiap 2-3 jam',
                    'Compression — Balut area cedera dengan perban elastis (tidak terlalu ketat)',
                    'Elevation — Tinggikan bagian yang cedera di atas posisi jantung untuk mengurangi bengkak',
                    'Jangan pijat atau urut area yang baru cedera dalam 48 jam pertama',
                    'Bersihkan luka lecet jika ada dengan air bersih dan tutup dengan plester',
                ],
                'prevention' => ['Gunakan pelindung saat berkendara (sarung tangan, pelindung lutut/siku)', 'Pemanasan sebelum aktivitas berat', 'Pakai alas kaki yang stabil', 'Waspada di jalan licin atau tidak rata', 'Jaga keseimbangan tubuh saat beraktivitas'],
                'follow_up_questions' => ['Apakah bagian yang cedera bisa digerakkan sama sekali?', 'Apakah bengkaknya sangat besar atau ada perubahan bentuk?', 'Apakah ada luka terbuka atau tulang yang tampak menonjol?', 'Sudah berapa jam sejak kejadian?'],
                'recommendation' => 'Kemungkinan besar cedera jaringan lunak (sprain/strain). Terapkan RICE segera, gunakan anti-nyeri jika diperlukan, dan evaluasi ke dokter jika bengkak tidak membaik dalam 48 jam atau ada kecurigaan fraktur.',
                'tldr' => 'Cedera jaringan lunak akibat jatuh/benturan. Lakukan RICE (Istirahat, Es, Kompres, Tinggikan), minum anti-nyeri sesuai kebutuhan, dan periksa ke dokter jika bengkak berat atau tidak membaik.',
            ],
            'skin_wound' => [
                'summary_prefix' => 'Keluhan kamu mengarah ke luka terbuka atau abrasi pada kulit yang memerlukan perawatan luka yang tepat. Keluhan utama yang saya tangkap: ',
                'condition' => 'Luka terbuka / abrasi kulit',
                'possible_causes' => [
                    ['name' => 'Abrasi (luka lecet)', 'percentage' => 45, 'reason' => 'Gesekan kulit dengan permukaan kasar menyebabkan lapisan kulit terkelupas.'],
                    ['name' => 'Laserasi (luka robek)', 'percentage' => 35, 'reason' => 'Benturan atau benda tajam menyebabkan kulit robek atau terbuka.'],
                    ['name' => 'Luka tusuk/sayat', 'percentage' => 20, 'reason' => 'Benda tajam menembus atau mengiris kulit.'],
                ],
                'related_symptoms' => ['perdarahan', 'nyeri area luka', 'kemerahan sekitar luka', 'bengkak ringan', 'cairan keluar dari luka'],
                'diseases' => [
                    [
                        'name' => 'Luka terbuka',
                        'description' => 'Luka terbuka adalah kerusakan pada kulit yang mengekspos jaringan di bawahnya. Risiko utama adalah infeksi jika tidak dibersihkan dan dirawat dengan benar.',
                        'relation_to_case' => 'Luka dari jatuh atau kecelakaan perlu dibersihkan segera untuk mencegah infeksi.',
                    ],
                ],
                'triggers' => ['Jatuh atau terpeleset', 'Kecelakaan lalu lintas', 'Terkena benda tajam', 'Gesekan dengan permukaan kasar'],
                'active_ingredients' => ['povidone iodine', 'chlorhexidine', 'neomycin'],
                'medicines' => [
                    [
                        'name' => 'Povidone Iodine (Betadine)',
                        'function' => 'Antiseptik untuk membersihkan luka dan mencegah infeksi bakteri.',
                        'dosage' => 'Oleskan tipis pada area luka 1-2 kali sehari',
                        'how_to_take' => 'Bersihkan luka dengan air bersih terlebih dahulu, lalu oleskan antiseptik',
                        'duration' => 'Sampai luka mulai kering dan menutup',
                        'when_to_take' => 'Setelah membersihkan luka, saat ganti perban',
                        'halal_status' => 'Perlu cek label produsen',
                        'safety_note' => 'Hindari pada luka yang sangat dalam atau luka bakar luas. Hentikan jika iritasi.',
                        'side_effects' => ['rasa perih sementara', 'perubahan warna kulit sementara'],
                    ],
                    [
                        'name' => 'Salep Antibiotik (Neomycin/Gentamicin)',
                        'function' => 'Mencegah dan mengatasi infeksi bakteri pada luka terbuka.',
                        'dosage' => 'Oleskan tipis pada luka 2-3 kali sehari sesuai kemasan',
                        'how_to_take' => 'Oleskan setelah luka dibersihkan, tutup dengan kasa steril',
                        'duration' => '5-7 hari atau sampai luka menutup',
                        'when_to_take' => 'Setiap kali ganti perban',
                        'halal_status' => 'Perlu cek komposisi dan produsen',
                        'safety_note' => 'Hentikan jika muncul ruam atau gatal berlebih di sekitar luka.',
                        'side_effects' => ['iritasi lokal ringan', 'reaksi alergi jarang'],
                    ],
                    [
                        'name' => 'Paracetamol',
                        'function' => 'Membantu meredakan nyeri akibat luka.',
                        'dosage' => 'Ikuti dosis pada kemasan',
                        'how_to_take' => 'Diminum saat nyeri terasa mengganggu',
                        'duration' => 'Beberapa hari sampai nyeri mereda',
                        'when_to_take' => 'Saat diperlukan',
                        'halal_status' => 'Perlu cek label produsen',
                        'safety_note' => 'Jangan melebihi dosis maksimum harian.',
                        'side_effects' => ['mual ringan'],
                    ],
                ],
                'drug_mechanism' => 'Antiseptik membunuh bakteri pada permukaan luka. Salep antibiotik mencegah pertumbuhan bakteri lebih lanjut. Paracetamol mengurangi persepsi nyeri di sistem saraf pusat.',
                'usage_instructions' => 'Cuci tangan sebelum merawat luka. Bersihkan luka dengan air mengalir, keringkan, oleskan antiseptik, lalu tutup dengan kasa steril. Ganti perban minimal 1-2 kali sehari.',
                'diet_advice' => 'Perbanyak protein dan vitamin C untuk mempercepat penyembuhan luka. Minum air putih yang cukup. Hindari makanan yang mengganggu proses penyembuhan seperti alkohol.',
                'first_aid' => [
                    'Tekan luka dengan kain bersih jika berdarah',
                    'Bersihkan luka dengan air mengalir bersih',
                    'Oleskan antiseptik seperti Betadine',
                    'Tutup luka dengan plester atau kasa steril',
                    'Ke dokter jika luka dalam, lebar, atau tidak berhenti berdarah dalam 10 menit',
                ],
                'prevention' => ['Gunakan pelindung saat berkendara', 'Hati-hati dengan benda tajam', 'Pastikan vaksin tetanus masih berlaku', 'Simpan kotak P3K di rumah'],
                'follow_up_questions' => ['Apakah pendarahannya sudah berhenti?', 'Seberapa dalam lukanya?', 'Apakah ada benda asing di dalam luka?', 'Kapan terakhir vaksin tetanus?'],
                'recommendation' => 'Bersihkan luka segera dengan air bersih dan antiseptik, tutup dengan kasa steril. Ke dokter jika luka dalam, tidak berhenti berdarah, atau ada tanda infeksi (merah meluas, nanah, demam).',
                'tldr' => 'Luka terbuka perlu dibersihkan segera dan ditutup steril. Gunakan antiseptik dan salep antibiotik untuk mencegah infeksi. Ke dokter jika luka dalam atau berdarah terus.',
            ],
            'muscle_pain' => [
                'summary_prefix' => 'Keluhan kamu paling mengarah ke nyeri otot atau gangguan muskuloskeletal. Keluhan utama yang saya tangkap: ',
                'condition' => 'Nyeri otot (myalgia) / gangguan muskuloskeletal',
                'possible_causes' => [
                    ['name' => 'Ketegangan otot (muscle strain)', 'percentage' => 45, 'reason' => 'Otot tegang akibat postur buruk, aktivitas berat, atau kurang peregangan.'],
                    ['name' => 'Kelelahan otot', 'percentage' => 30, 'reason' => 'Penggunaan otot berlebihan atau aktivitas fisik yang tidak biasa.'],
                    ['name' => 'Peradangan sendi ringan', 'percentage' => 25, 'reason' => 'Bila nyeri terlokalisir di area sendi dan disertai kaku.'],
                ],
                'related_symptoms' => ['pegal-pegal', 'kaku otot', 'nyeri saat bergerak', 'kram', 'keterbatasan gerak', 'nyeri menjalar'],
                'diseases' => [
                    [
                        'name' => 'Myalgia',
                        'description' => 'Myalgia adalah nyeri pada otot yang bisa disebabkan oleh kelelahan, postur yang buruk, stres, atau aktivitas fisik berlebihan.',
                        'relation_to_case' => 'Keluhan pegal, kram, atau nyeri otot sangat umum dan biasanya membaik dengan istirahat dan terapi sederhana.',
                    ],
                    [
                        'name' => 'Tension Myositis',
                        'description' => 'Ketegangan kronis pada otot yang sering muncul di area leher, bahu, dan punggung, biasanya terkait stres dan postur.',
                        'relation_to_case' => 'Jika nyeri dominan di leher, bahu, atau punggung dan memburuk saat bekerja lama.',
                    ],
                ],
                'triggers' => ['Postur tubuh yang buruk saat duduk/bekerja', 'Aktivitas fisik berat tanpa pemanasan', 'Kurang olahraga atau terlalu banyak duduk', 'Stres dan ketegangan mental', 'Tidur dengan posisi yang salah'],
                'active_ingredients' => ['paracetamol', 'methyl salicylate', 'ibuprofen'],
                'medicines' => [
                    [
                        'name' => 'Paracetamol',
                        'function' => 'Membantu meredakan nyeri otot ringan hingga sedang.',
                        'dosage' => 'Ikuti dosis pada kemasan',
                        'how_to_take' => 'Diminum saat nyeri muncul',
                        'duration' => '1-3 hari untuk keluhan akut',
                        'when_to_take' => 'Saat diperlukan, setiap 4-6 jam',
                        'halal_status' => 'Perlu cek label produsen',
                        'safety_note' => 'Jangan melebihi dosis maksimum harian.',
                        'side_effects' => ['mual ringan', 'reaksi alergi jarang'],
                    ],
                    [
                        'name' => 'Koyo/Salep Methyl Salicylate (Salonpas/Counterpain)',
                        'function' => 'Pereda nyeri topikal yang memberikan efek hangat dan mengurangi nyeri otot lokal.',
                        'dosage' => 'Tempel koyo atau oleskan salep pada area nyeri 2-3 kali sehari',
                        'how_to_take' => 'Untuk pemakaian luar saja, hindari area luka terbuka',
                        'duration' => 'Beberapa hari sampai nyeri mereda',
                        'when_to_take' => 'Saat nyeri muncul, terutama saat istirahat',
                        'halal_status' => 'Perlu cek komposisi dan produsen',
                        'safety_note' => 'Hentikan jika iritasi kulit. Jangan gunakan bersamaan dengan kompres panas.',
                        'side_effects' => ['rasa panas/dingin lokal', 'iritasi kulit pada sebagian orang'],
                    ],
                    [
                        'name' => 'Ibuprofen',
                        'function' => 'Anti-inflamasi yang membantu jika nyeri otot disertai peradangan.',
                        'dosage' => 'Ikuti dosis pada kemasan',
                        'how_to_take' => 'Diminum setelah makan',
                        'duration' => '3-5 hari jika ada peradangan',
                        'when_to_take' => 'Setiap 6-8 jam sesuai kebutuhan',
                        'halal_status' => 'Perlu cek label dan produsen',
                        'safety_note' => 'Hindari jika ada riwayat maag. Konsultasi jika keluhan kronis.',
                        'side_effects' => ['gangguan lambung', 'mual'],
                    ],
                ],
                'drug_mechanism' => 'Paracetamol bekerja mengurangi persepsi nyeri di otak. Methyl salicylate memberikan efek counter-irritant (mengalihkan sinyal nyeri) dan vasodilatasi lokal untuk meningkatkan aliran darah. Ibuprofen menghambat prostaglandin yang menyebabkan nyeri dan peradangan.',
                'usage_instructions' => 'Kombinasikan obat dengan stretching ringan, kompres hangat, dan perbaikan postur. Jangan hanya mengandalkan obat tanpa memperbaiki kebiasaan yang menjadi pemicu.',
                'diet_advice' => 'Pastikan asupan magnesium cukup (pisang, kacang, sayuran hijau) untuk mencegah kram. Cukupi cairan dan protein untuk pemulihan otot. Kurangi kafein berlebihan yang bisa memperburuk kram.',
                'first_aid' => [
                    'Kompres hangat pada area yang nyeri selama 15-20 menit',
                    'Lakukan stretching ringan dan perlahan',
                    'Hindari aktivitas berat sementara',
                    'Pijat ringan area sekitar yang tegang (bukan langsung di titik nyeri)',
                    'Mandi air hangat untuk merelaksasi otot',
                ],
                'prevention' => ['Pemanasan sebelum olahraga', 'Perbaiki postur saat duduk dan bekerja', 'Olahraga rutin ringan', 'Stretching sebelum tidur dan setelah bangun', 'Jaga hidrasi yang cukup'],
                'follow_up_questions' => ['Apakah nyeri muncul setelah aktivitas tertentu?', 'Apakah ada rasa kebas, kesemutan, atau nyeri menjalar ke kaki/tangan?', 'Sudah berapa lama keluhan berlangsung?', 'Apakah pernah mengalami cedera di area yang sama?'],
                'recommendation' => 'Keluhan nyeri otot biasanya membaik dengan istirahat, stretching, dan obat pereda nyeri. Jika nyeri menjalar, disertai kebas, atau tidak membaik dalam 1 minggu, konsultasi dokter.',
                'tldr' => 'Nyeri otot umumnya terkait postur, kelelahan, atau aktivitas. Kompres hangat, stretching, dan obat pereda nyeri biasanya cukup. Waspadai jika ada gejala saraf (kebas/kesemutan menjalar).',
            ],
            default => [
                'summary_prefix' => 'Saya telah menganalisis input Anda, namun keluhan yang disampaikan belum cukup spesifik untuk mendiagnosis kondisi secara akurat. Input yang tercatat: ',
                'condition' => 'Keluhan tidak spesifik / Perlu detail tambahan',
                'possible_causes' => [
                    ['name' => 'Gejala terlalu umum', 'percentage' => 45, 'reason' => 'Banyak kondisi kesehatan memiliki gejala awal yang serupa.'],
                    ['name' => 'Informasi tidak lengkap', 'percentage' => 35, 'reason' => 'Membutuhkan detail seperti lokasi nyeri, durasi, dan tingkat keparahan.'],
                    ['name' => 'Salah ketik / Input acak', 'percentage' => 20, 'reason' => 'Teks yang dimasukkan mungkin tidak mengandung kata kunci medis yang jelas.'],
                ],
                'related_symptoms' => ['lemas', 'tidak nyaman', 'nyeri ringan', 'kurang fit'],
                'diseases' => [
                    [
                        'name' => 'Analisis Tertunda',
                        'description' => 'Sebagai asisten medis AI, saya membutuhkan deskripsi gejala yang lebih detail. Misalnya: "kepala pusing sebelah kiri", "perut mual setelah makan pedas", atau "tangan terkilir jatuh dari motor".',
                        'relation_to_case' => 'Mohon jelaskan kembali keluhan Anda dengan kalimat yang lebih deskriptif.',
                    ]
                ],
                'triggers' => ['Kurang istirahat', 'Kelelahan ringan', 'Dehidrasi'],
                'active_ingredients' => ['paracetamol', 'multivitamin'],
                'medicines' => [
                    [
                        'name' => 'Paracetamol',
                        'function' => 'Meredakan nyeri ringan atau demam jika ada.',
                        'dosage' => '1 tablet 500mg, bila perlu',
                        'how_to_take' => 'Sesudah makan',
                        'duration' => 'Hanya jika timbul gejala',
                        'when_to_take' => 'Sesuai kebutuhan',
                        'halal_status' => 'Perlu cek sertifikasi halal pada kemasan (Titik kritis: Cangkang Kapsul/Gelatin)',
                        'safety_note' => 'Hentikan penggunaan jika tidak ada perbaikan.',
                        'side_effects' => ['mual ringan'],
                    ]
                ],
                'drug_mechanism' => 'Obat simptomatik seperti Paracetamol bekerja dengan menghambat prostaglandin di otak untuk mengurangi sinyal nyeri.',
                'usage_instructions' => 'Saat ini sebaiknya perbanyak istirahat dan minum air putih. Jika keluhan memburuk, segera perjelas gejala Anda atau konsultasi ke dokter.',
                'diet_advice' => 'Konsumsi makanan bergizi dan pastikan hidrasi tubuh tercukupi (minimal 2 liter air per hari).',
                'first_aid' => ['Istirahat yang cukup', 'Perbanyak minum air putih', 'Evaluasi gejala dalam 24 jam ke depan'],
                'prevention' => ['Tidur 7-8 jam per hari', 'Olahraga teratur', 'Manajemen stres yang baik'],
                'follow_up_questions' => ['Bisakah Anda mendeskripsikan ulang bagian tubuh mana yang sakit?', 'Sudah berapa hari Anda merasakan keluhan ini?', 'Apakah ada faktor pemicu tertentu (seperti makanan atau aktivitas)?'],
                'recommendation' => 'Ketik ulang keluhan Anda dengan lebih rinci agar saya bisa memberikan analisis medis dan rekomendasi obat yang sangat akurat.',
                'tldr' => 'Saya memerlukan detail lebih spesifik mengenai keluhan Anda (lokasi, durasi, dan rasa sakitnya) untuk memberikan analisis yang akurat.',
            ],
        };
    }

    private function extractActiveIngredientsFromPrompt(string $prompt): array
    {
        $lower = Str::lower($prompt);

        return match (true) {
            Str::contains($lower, ['maag', 'lambung', 'mual', 'perut']) => ['antasida', 'omeprazole'],
            Str::contains($lower, ['batuk', 'pilek']) => ['dextromethorphan', 'cetirizine'],
            Str::contains($lower, ['demam', 'nyeri', 'sakit kepala']) => ['paracetamol'],
            Str::contains($lower, ['diare']) => ['oralit', 'zinc'],
            Str::contains($lower, ['terkilir', 'keseleo', 'jatuh', 'cedera', 'bengkak', 'memar', 'motor']) => ['ibuprofen', 'paracetamol', 'methyl salicylate'],
            Str::contains($lower, ['luka', 'berdarah', 'lecet', 'sayat', 'tergores']) => ['povidone iodine', 'neomycin', 'paracetamol'],
            Str::contains($lower, ['pegal', 'kram', 'otot', 'punggung', 'pinggang', 'sendi']) => ['paracetamol', 'methyl salicylate', 'ibuprofen'],
            default => ['paracetamol'],
        };
    }

    private function referenceRangeForTest(string $testName): string
    {
        $name = Str::lower($testName);

        return match (true) {
            Str::contains($name, ['glucose', 'gula darah']) => '70-99 mg/dL (puasa)',
            Str::contains($name, ['cholesterol', 'kolesterol']) => '< 200 mg/dL',
            Str::contains($name, ['hemoglobin', 'hb']) => '12-17 g/dL',
            Str::contains($name, ['uric', 'asam urat']) => '3.5-7.2 mg/dL',
            default => 'Lihat rentang rujukan laboratorium setempat',
        };
    }

    private function statusForLabValue(string $testName, float $value): string
    {
        $name = Str::lower($testName);

        return match (true) {
            Str::contains($name, ['glucose', 'gula darah']) && $value > 126 => 'Tinggi',
            Str::contains($name, ['glucose', 'gula darah']) && $value > 0 && $value < 70 => 'Rendah',
            Str::contains($name, ['cholesterol', 'kolesterol']) && $value >= 200 => 'Tinggi',
            Str::contains($name, ['hemoglobin', 'hb']) && $value > 0 && $value < 12 => 'Rendah',
            Str::contains($name, ['asam urat', 'uric']) && $value > 7.2 => 'Tinggi',
            default => 'Normal',
        };
    }

    private function interpretLabValue(string $testName, float $value): string
    {
        $status = $this->statusForLabValue($testName, $value);

        return match ($status) {
            'Tinggi' => 'Nilai berada di atas kisaran rujukan umum dan perlu dilihat bersama gejala serta riwayat klinis.',
            'Rendah' => 'Nilai berada di bawah kisaran rujukan umum dan dapat memerlukan evaluasi lebih lanjut.',
            default => 'Nilai tampak berada dalam kisaran umum, namun interpretasi final tetap mengikuti rujukan laboratorium.',
        };
    }
}
