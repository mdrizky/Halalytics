<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Check drug interaction between two medicines
     */
    public function checkDrugInteraction($drugA, $drugB)
    {
        $prompt = "Analisis interaksi obat antara {$drugA['name']} ({$drugA['generic_name']}) dan {$drugB['name']} ({$drugB['generic_name']}). 

Berikan analisis dalam format JSON yang valid tanpa markdown formatting:
{
    \"has_interaction\": true/false,
    \"severity\": \"minor/moderate/major\",
    \"description\": \"penjelasan singkat dalam Bahasa Indonesia\",
    \"recommendation\": \"saran untuk pasien dalam Bahasa Indonesia\",
    \"scientific_basis\": \"dasar ilmiah dalam Bahasa Indonesia\"
}";

        return $this->generateText($prompt);
    }

    /**
     * Identify pill from image
     */
    public function identifyPill($imageBase64, $shape = null, $color = null)
    {
        $prompt = "Identifikasi obat/pil dari gambar ini. ";
        
        if ($shape) $prompt .= "Bentuk yang dilaporkan: {$shape}. ";
        if ($color) $prompt .= "Warna yang dilaporkan: {$color}. ";
        
        $prompt .= "Berikan hasil dalam format JSON yang valid tanpa markdown formatting:
{
    \"possible_drugs\": [
        {
            \"name\": \"nama obat\",
            \"confidence\": 0.95,
            \"generic_name\": \"nama generik\",
            \"description\": \"deskripsi obat dalam Bahasa Indonesia\"
        }
    ],
    \"visual_features\": {
        \"shape\": \"bentuk terdeteksi\",
        \"color\": \"warna terdeteksi\",
        \"imprint\": \"tulisan/logo terdeteksi\"
    }
}";

        return $this->generateWithImage($prompt, $imageBase64);
    }

    /**
     * Analyze lab results from image or data
     */
    public function analyzeLabResult($imageBase64 = null, $labData = [])
    {
        if ($imageBase64) {
            $prompt = "Analisis hasil laboratorium dari gambar ini. Identifikasi tes, nilai, satuan, dan status (normal/low/high). 

Berikan interpretasi dalam Bahasa Indonesia yang ramah dalam format JSON valid tanpa markdown formatting:
{
    \"detected_tests\": [
        {
            \"test_name\": \"nama tes\",
            \"value\": 100,
            \"unit\": \"satuan\",
            \"status\": \"normal/low/high\",
            \"interpretation\": \"penjelasan singkat\"
        }
    ],
    \"overall_assessment\": \"kesimpulan umum dalam Bahasa Indonesia\",
    \"lifestyle_recommendations\": [\"saran 1\", \"saran 2\"]
}";

            return $this->generateWithImage($prompt, $imageBase64);
        } else {
            $testsInfo = json_encode($labData);
            $prompt = "Berdasarkan hasil lab berikut: {$testsInfo}. Berikan analisis medis yang mudah dipahami dalam Bahasa Indonesia, termasuk saran gaya hidup dan kapan harus ke dokter. Format JSON valid.";

            return $this->generateText($prompt);
        }
    }

    /**
     * Find halal alternatives for a drug
     */
    public function findHalalAlternative($drug)
    {
        $ingredients = is_array($drug['ingredients']) ? implode(', ', $drug['ingredients']) : ($drug['ingredients'] ?? 'tidak diketahui');
        $prompt = "Obat {$drug['name']} mengandung: {$ingredients}. Identifikasi bahan tidak halal dan cari alternatif obat yang 100% halal dengan fungsi sama di Indonesia. 

Format JSON valid tanpa markdown formatting:
{
    \"problematic_ingredients\": [\"bahan 1\"],
    \"halal_alternatives\": [
        {
            \"name\": \"nama obat alternatif\",
            \"manufacturer\": \"produsen\",
            \"halal_cert\": \"info sertifikat\",
            \"confidence\": 0.95
        }
    ],
    \"explanation\": \"penjelasan dalam Bahasa Indonesia\"
}";

        return $this->generateText($prompt);
    }

    /**
     * Find halal alternatives for a consumer product (food, drinks, snacks, etc.)
     */
    public function findProductHalalAlternative($productName, $ingredients, $category)
    {
        $ingredientInfo = $ingredients ?: 'tidak diketahui';
        $categoryInfo = $category ?: 'makanan/minuman umum';

        $prompt = "Kamu adalah pakar produk konsumen halal di Indonesia. Produk '{$productName}' (kategori: {$categoryInfo}) mengandung bahan: {$ingredientInfo}.

Identifikasi bahan-bahan yang bermasalah (non-halal atau syubhat) dan berikan 3 alternatif produk halal bersertifikat yang tersedia di Indonesia dengan fungsi/rasa serupa.

PENTING: Fokus pada produk yang benar-benar ada dan populer di Indonesia (contoh: Indomie, Sedaap, Gaga, Wardah, dll).

Berikan hasil dalam format JSON valid tanpa markdown formatting:
{
    \"problematic_ingredients_reason\": \"penjelasan singkat kenapa produk ini bermasalah dari sisi halal\",
    \"halal_alternatives\": [
        {
            \"name\": \"nama produk alternatif halal\",
            \"manufacturer\": \"nama produsen\",
            \"brand\": \"nama merk/brand\",
            \"reason_it_is_better\": \"penjelasan singkat kenapa produk ini lebih baik (halal, sertifikasi MUI, dll)\"
        }
    ],
    \"explanation\": \"penjelasan umum dan saran untuk konsumen Muslim dalam Bahasa Indonesia\"
}";

        return $this->generateText($prompt);
    }

    /**
     * Generate custom voice message for reminders
     */
    public function generateVoiceMessage($userName, $drugName, $dosage, $time)
    {
        $prompt = "Buat pesan pengingat minum obat personal untuk {$userName} yang minum {$drugName} dosis {$dosage} pada jam {$time}. 

Berikan 3 variasi (formal, santai, motivasi) dalam Bahasa Indonesia. Format JSON valid:
{
    \"formal\": \"...\",
    \"casual\": \"...\",
    \"motivational\": \"...\"
}";

        return $this->generateText($prompt);
    }

    /**
     * Analyze symptoms and recommend active ingredients
     */
    public function analyzeSymptoms($symptoms, $userContext = [])
    {
        $userContextStr = !empty($userContext) ? "Informasi profil kesehatan pengguna: " . json_encode($userContext) . ". Gunakan informasi ini untuk memberikan analisis dan peringatan yang personal (sesuaikan dengan usia, jenis kelamin, alergi, dan riwayat medis)." : "";

        $prompt = "Kamu adalah sistem pakar medis kecerdasan buatan. Analisis gejala berikut: '{$symptoms}'. 
$userContextStr

Identifikasi kemungkinan kondisi medis (fokus pada kasus ringan/mild), bahan aktif (active ingredients) yang direkomendasikan, tingkat keparahan, peringatan darurat jika perlu, instruksi penggunaan umum, saran gaya hidup (penanganan non-obat), dan rekomendasi akhir.
Juga berikan cek halal singkat untuk bahan aktif tersebut.

PENTING: Berikan rekomendasi 'list_obat' yang tersedia di Indonesia (baik generik maupun merk populer).

Berikan analisis dalam format JSON yang valid tanpa markdown formatting:
{
    \"condition\": \"nama kondisi dalam Bahasa Indonesia\",
    \"gejala_terkait\": [\"gejala 1\", \"gejala 2\"],
    \"recommended_ingredients\": [\"bahan aktif 1\", \"bahan aktif 2\"],
    \"severity\": \"mild/moderate/emergency\",
    \"emergency_warning\": \"null atau pesan jika gawat\",
    \"halal_check\": {
        \"status\": \"halal/syubhat/haram\",
        \"notes\": \"penjelasan singkat tentang kehalalan bahan aktif tersebut\"
    },
    \"usage_instructions\": \"cara pemakaian umum\",
    \"lifestyle_advice\": \"saran pola hidup/penanganan tanpa obat\",
    \"dosage_guidelines\": \"aruran dosis umum untuk bahan aktif tersebut\",
    \"recommended_medicines_list\": [\"Obat A\", \"Obat B (Generik)\"],
    \"recommendation\": \"kesimpulan saran penutup\"
}";

        return $this->generateText($prompt);
    }

    /**
     * Analyze BPOM product registration using AI
     */
    public function analyzeBpomProduct($code)
    {
        $prompt = "Kamu adalah ahli regulasi BPOM Indonesia. Identifikasi produk dengan nomor registrasi BPOM: '$code'.

Berikan informasi dalam format JSON berikut:
{
    \"found\": true/false,
    \"nomor_reg\": \"nomor registrasi lengkap\",
    \"nama_produk\": \"nama produk\",
    \"merk\": \"merk/brand\",
    \"kategori\": \"obat/kosmetik/pangan/suplemen/obat_tradisional/obat_kuasi\",
    \"pendaftar\": \"nama perusahaan pendaftar\",
    \"alamat_produsen\": \"alamat produsen\",
    \"bentuk_sediaan\": \"tablet/krim/cairan/serbuk/dll\",
    \"status_keamanan\": \"aman/waspada/bahaya\",
    \"pernah_ditarik\": false,
    \"catatan_keamanan\": \"catatan penting jika ada\",
    \"analisis_halal\": \"penjelasan singkat status halal berdasarkan jenis produk\",
    \"disclaimer\": \"Data berdasarkan informasi publik. Untuk validitas hukum, periksa situs resmi BPOM.\"
}

PENTING:
- Kode NA = Kosmetik Asia/Lokal, NC = Kosmetik Eropa, ND = Kosmetik Amerika
- Kode MD = Pangan Olahan Lokal, ML = Pangan Olahan Impor
- Kode D = Obat (DBL=Bebas Lokal, DKL=Keras Lokal)
- Kode TR/TI = Obat Tradisional, SD/SI = Suplemen
- Jika tidak yakin, set found=false dan berikan penjelasan";

        return $this->generateText($prompt);
    }

    /**
     * Analyze skincare/cosmetic ingredients from OCR text
     */
    public function analyzeSkincareIngredients($ingredientsText, $userContext = [])
    {
        $userContextStr = !empty($userContext) ? "Informasi profil kesehatan pengguna: " . json_encode($userContext) . ". Gunakan informasi ini untuk memberikan peringatan khusus jika ada bahan yang tidak cocok." : "";
        
        $prompt = "Kamu adalah ahli dermatologi dan kimia kosmetik. $userContextStr Analisis daftar bahan skincare/kosmetik berikut:

'$ingredientsText'

Berikan laporan lengkap dalam format JSON valid (tanpa markdown ```json).
Struktur JSON harus persis seperti ini:
{
    \"bahan_terdeteksi\": [
        {
            \"nama\": \"nama bahan INCI\",
            \"nama_umum\": \"nama umum bahasa Indonesia\",
            \"fungsi\": \"fungsi dalam produk\",
            \"tingkat_bahaya\": 1-10,
            \"status_halal\": \"halal/haram/syubhat/aman\",
            \"catatan_halal\": \"penjelasan titik kritis halal jika ada\",
            \"peringatan\": \"peringatan khusus jika ada\"
        }
    ],
    \"bahan_berbahaya\": [\"list bahan yang berbahaya/dilarang BPOM\"],
    \"bahan_syubhat\": [\"list bahan yang titik kritis halal (seperti Glycerin, Stearic Acid, Collagen, Gelatin, Placenta)\"],
    \"skor_keamanan\": 1-100,
    \"status_keamanan\": \"aman/waspada/bahaya\",
    \"status_halal\": \"halal/haram/syubhat/belum_diverifikasi\",
    \"cocok_untuk\": [\"jenis kulit yang cocok\"],
    \"tidak_cocok_untuk\": [\"jenis kulit / kondisi yang tidak cocok\"],
    \"ringkasan\": \"ringkasan singkat dalam bahasa Indonesia untuk pengguna awam\",
    \"disclaimer\": \"Analisis ini berdasarkan database bahan kosmetik internasional. Status halal resmi harus dikonfirmasi dengan sertifikat MUI/BPJPH.\"
}

PENTING:
- JSON Only. Jangan ada teks lain sebelum atau sesudah JSON.
- Perhatikan bahan-bahan kritis halal:
- Glycerin/Gliserin: bisa dari nabati atau hewani
- Stearic Acid: bisa dari lemak hewan
- Collagen/Kolagen: sering dari hewan (sapi/ikan/babi)
- Gelatin: umumnya dari hewan
- Placenta: dari hewan
- Alcohol/Ethanol: kontroversial dalam fiqih
- Carmine/CI 75470: dari serangga cochineal";

        return $this->generateText($prompt);
    }

    /**
     * General product safety analysis (food, cosmetics, medicines)
     */
    public function analyzeProductSafety($productName, $ingredientsText, $category = 'umum', $userContext = [])
    {
        $userContextStr = !empty($userContext) ? "Informasi profil kesehatan pengguna: " . json_encode($userContext) . ". Gunakan informasi ini untuk memberikan skor keamanan dan peringatan yang personal." : "";

        $prompt = "Kamu adalah ahli keamanan produk konsumen di Indonesia. $userContextStr Analisis produk berikut:

Nama Produk: '$productName'
Kategori: '$category'
Daftar Bahan: '$ingredientsText'

Berikan analisis keamanan dalam format JSON:
{
    \"nama_produk\": \"$productName\",
    \"kategori\": \"$category\",
    \"skor_keamanan\": 1-100,
    \"status_keamanan\": \"aman/waspada/bahaya\",
    \"status_halal\": \"halal/haram/syubhat/belum_diverifikasi\",
    \"alasan_halal\": \"penjelasan singkat\",
    \"bahan_berbahaya\": [{\"nama\": \"...\", \"bahaya\": \"...\"}],
    \"bahan_kritis_halal\": [{\"nama\": \"...\", \"status\": \"...\", \"alasan\": \"...\"}],
    \"rekomendasi\": \"saran untuk pengguna\",
    \"cocok_untuk_bumil\": true/false,
    \"cocok_untuk_anak\": true/false,
    \"alergen\": [\"list alergen yang terdeteksi\"]
}";

        return $this->generateText($prompt);
    }

    /**
     * Analyze general ingredients text for food/general products
     */
    public function analyzeIngredients($ingredientsText, $userContext = [])
    {
        $userContextStr = !empty($userContext) ? "Informasi profil kesehatan pengguna: " . json_encode($userContext) : "";
        
        $prompt = "Kamu adalah ahli nutrisi dan keamanan pangan. $userContextStr. Analisis daftar bahan/komposisi berikut:
        
'$ingredientsText'

Berikan laporan lengkap dalam format JSON valid (tanpa markdown). 
JSON harus berisi:
{
    \"ingredients\": [
        {
            \"name\": \"nama bahan\",
            \"halal_status\": \"halal/haram/syubhat\",
            \"safety_level\": \"safe/warning/danger\",
            \"description\": \"penjelasan singkat\",
            \"health_impact\": \"dampak kesehatan\",
            \"is_personal_allergen\": true/false
        }
    ],
    \"nutrition_estimate\": {
        \"sugar_g\": angka,
        \"sodium_mg\": angka,
        \"calories\": angka,
        \"fat_g\": angka
    },
    \"health_warnings\": [\"list peringatan umum\"],
    \"personal_warnings\": [\"list peringatan spesifik berdasarkan profil kesehatan pengguna\"],
    \"status_halal\": \"halal/haram/syubhat\",
    \"status_kesehatan\": \"sehat/tidak_sehat/perlu_riset\",
    \"skor_kesehatan\": 1-100,
    \"ringkasan\": \"penjelasan singkat untuk pengguna\"
}

PENTING:
- Estimasi nutrisi berdasarkan standar umum per sajian (estimasi terbaik).
- Jika user memiliki riwayat medis (seperti diabetes atau hipertensi), sesuaikan 'personal_warnings' dan 'skor_kesehatan'.";

        return $this->generateText($prompt);
    }

    /**
     * Compare multiple products side-by-side
     */
    public function compareProducts($products, $userContext = [])
    {
        $userContextStr = !empty($userContext) ? "Informasi profil kesehatan pengguna: " . json_encode($userContext) : "";
        $productsJson = json_encode($products);
        
        $prompt = "Kamu adalah ahli gizi dan penasehat belanja sehat. $userContextStr. 
        Bandingkan produk-produk berikut secara detail:
        
        $productsJson
        
        Berikan analisis perbandingan dalam format JSON valid:
        {
            \"comparison\": [
                {
                    \"product_name\": \"nama produk\",
                    \"halal_score\": 1-100,
                    \"safety_score\": 1-100,
                    \"pros\": [\"kelebihan 1\", \"kelebihan 2\"],
                    \"cons\": [\"kekurangan 1\", \"kekurangan 2\"],
                    \"suitability_notes\": \"catatan kecocokan dengan profil kesehatan\"
                }
            ],
            \"better_choice\": \"nama produk yang paling direkomendasikan\",
            \"reason\": \"alasan utama rekomendasi\",
            \"summary\": \"ringkasan perbandingan untuk pengguna\"
        }";

        return $this->generateText($prompt);
    }

    /**
     * Generate content with a custom prompt
     */
    public function generateCustomContent($prompt)
    {
        return $this->generateText($prompt);
    }

    /**
     * Core text generation
     */
    public function generateText($prompt)
    {
        try {
            $prompt = $this->prependLocaleInstruction($prompt, false);
            $response = Http::timeout(60)->post(
                "{$this->baseUrl}/models/gemini-2.0-flash:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]
            );

            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return $this->decodeJson($text);
            }
            
            Log::error("Gemini API Error: " . $response->body());
            throw new \Exception('Gemini API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Gemini Service Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Core vision generation
     */
    public function generateWithImage($prompt, $imageBase64)
    {
        try {
            $prompt = $this->prependLocaleInstruction($prompt, true);
            $imageBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64);
            $response = Http::timeout(60)->post(
                "{$this->baseUrl}/models/gemini-2.0-flash:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => 'image/jpeg',
                                        'data' => $imageBase64
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return $this->decodeJson($text);
            }
            throw new \Exception('Gemini Vision API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Gemini Vision Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Clean and decode JSON
     */
    private function decodeJson($text)
    {
        // Remove markdown code blocks
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```/', '', $text);
        
        // Remove any text before the first '{' and after the last '}'
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        
        if ($start !== false && $end !== false) {
            $text = substr($text, $start, $end - $start + 1);
        }
        
        $text = trim($text);
        $decoded = json_decode($text, true);
        
        // Fallback: If decode fails, attempt to fix common issues or return raw wrapped
        if (!$decoded) {
            \Log::warning("JSON Decode Failed. Raw text: " . substr($text, 0, 100) . "...");
            return ['raw_text' => $text, 'error' => 'Failed to parse AI response'];
        }
        
        return $decoded;
    }

    /**
     * Prepend strict language instruction based on request locale.
     * JSON keys must remain unchanged for client compatibility.
     */
    private function prependLocaleInstruction(string $prompt, bool $isVision): string
    {
        $locale = strtolower((string) app()->getLocale());
        $languageLabel = match ($locale) {
            'en' => 'English',
            'ms' => 'Malay',
            'ar' => 'Arabic',
            default => 'Indonesian',
        };

        $modeLabel = $isVision ? 'image analysis' : 'text analysis';
        $instruction = "SYSTEM INSTRUCTION ({$modeLabel}): " .
            "Respond ONLY in {$languageLabel}. " .
            "If output is JSON, keep all JSON keys exactly as requested and translate only human-readable values. " .
            "Do not add extra wrappers or markdown.";

        return $instruction . "\n\n" . $prompt;
    }

    /**
     * Identify food candidates from an image
     */
    public function identifyFoodCandidates($imageBase64)
    {
        $prompt = "Identifikasi makanan dari gambar ini, khususnya makanan jalanan (street food) atau masakan Indonesia seperti Nasi Goreng, Mie Ayam, Sate, dll. 

Berikan hasil dalam format JSON yang valid tanpa markdown formatting:
{
    \"matches\": [
        {
            \"name\": \"nama makanan dalam Bahasa Indonesia\",
            \"confidence\": 0.95
        }
    ]
}";

        return $this->generateWithImage($prompt, $imageBase64);
    }

    /**
     * Analyze ingredients directly from an image (OCR + Health Analysis)
     */
    public function analyzeIngredientsFromImage($imageBase64, $userContext = [])
    {
        $userContextStr = !empty($userContext) ? "Informasi profil kesehatan pengguna: " . json_encode($userContext) : "";
        
        $prompt = "Kamu adalah ahli nutrisi, dermatologi, dan keamanan pangan. 
Tugasmu adalah melakukan OCR pada gambar label produk ini (fokus pada Komposisi/Ingredients) dan melakukan analisis mendalam.

$userContextStr

Berikan laporan lengkap dalam format JSON valid (tanpa markdown). 
JSON harus berisi:
{
    \"product_name\": \"nama produk terdeteksi\",
    \"brand\": \"merk terdeteksi\",
    \"ingredients\": [
        {
            \"name\": \"nama bahan\",
            \"halal_status\": \"halal/haram/syubhat\",
            \"safety_level\": \"safe/warning/danger\",
            \"description\": \"penjelasan singkat\",
            \"health_impact\": \"dampak kesehatan\",
            \"is_personal_allergen\": true/false
        }
    ],
    \"nutrition_estimate\": {
        \"sugar_g\": \"angka per sajian\",
        \"sodium_mg\": \"angka per sajian\",
        \"calories\": \"angka per sajian\",
        \"fat_g\": \"angka per sajian\"
    },
    \"health_warnings\": [\"list peringatan umum\"],
    \"personal_warnings\": [\"list peringatan spesifik berdasarkan profil kesehatan pengguna\"],
    \"status_halal\": \"halal/haram/syubhat\",
    \"status_kesehatan\": \"sehat/tidak_sehat/perlu_riset\",
    \"skor_kesehatan\": 1-100,
    \"ringkasan\": \"penjelasan singkat untuk pengguna (max 2 kalimat)\"
}

PENTING:
- Jika teks buram atau tidak terbaca, coba deteksi produk dari kemasan dan berikan info umum.
- Sesuaikan analisis dengan profil kesehatan user (misal: jika user diabetes, beri skor rendah pada produk tinggi gula).
- Pastikan JSON valid dan tidak ada teks lain.";

        return $this->generateWithImage($prompt, $imageBase64);
    }
}
