<?php

namespace App\Services\AI;

use App\Models\AiPrompt;
use Illuminate\Support\Facades\Log;

class PromptBuilderService
{
    /** Maps logical prompt types to ai_prompts.feature_key */
    private const TYPE_MAP = [
        'food_analysis' => 'food_analysis',
        'halal_check' => 'halal_check',
        'health_check' => 'health_check',
        'user_chat' => 'user_chat',
        'product_comparison' => 'product_comparison',
        'risk_analysis' => 'risk_analysis',
        'recommendation' => 'recommendation',
    ];

    /**
     * Build a full prompt string from DB template or fallback, with variable injection.
     *
     * @param  array<string, mixed>  $variables
     */
    public function build(string $featureKey, array $variables = [], ?string $fallbackTemplate = null): string
    {
        // If fallbackTemplate is provided, use it directly (allows code-level override)
        $template = $fallbackTemplate;

        if (!$template) {
            $record = AiPrompt::forFeature($featureKey);

            $template = $record?->system_prompt ?? $this->defaultTemplate($featureKey);
        }

        return $this->injectVariables($template, $variables);
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    private function injectVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $template = str_replace(
                ['{' . $key . '}', '{{' . $key . '}}'],
                (string) $value,
                $template
            );
        }

        return $template;
    }

    private function defaultTemplate(string $type): string
    {
        return match ($type) {
            'user_chat' => <<<PROMPT
Anda adalah asisten medis dan nutrisi proaktif di aplikasi Halalytics.
Nama User: {user_name} (Umur: {user_age})
Riwayat Penyakit: {user_diseases}
Alergi: {user_allergies}

Konteks Percakapan: {user_message}

ATURAN WAJIB:
1. Langsung ke poin. DILARANG menggunakan pembuka seperti "Halo", "Tentu saja", "Saya adalah AI", atau basa-basi lainnya.
2. Jawab pertanyaan user secara spesifik, menggunakan bahasa Indonesia baku yang ramah.
3. Berikan saran praktis berdasarkan profil medis user (penyakit & alergi). Jika ada risiko berbahaya, tegaskan dengan jelas.
4. Gunakan poin-poin singkat jika menjelaskan prosedur atau daftar.
PROMPT,

            'food_analysis' => <<<PROMPT
Anda adalah sistem analis Halalytics. Misi Anda adalah membedah profil produk secara objektif.

Data User:
- Nama: {user_name} ({user_age} tahun)
- Penyakit: {user_diseases}
- Alergi: {user_allergies}

Data Produk:
- Nama: {product_name} (Barcode: {barcode})
- Kategori: {product_category}
- Komposisi: {ingredients_text}
- Nutrisi (per 100g): Gula={sugars}g | Sodium={sodium}mg | Lemak={fat}g | Protein={protein}g | Kalori={calories}kcal
- Status Halal Eksternal: {halal_label}

ATURAN WAJIB:
1. FORMAT OUTPUT: HANYA KEMBALIKAN JSON VALID tanpa tambahan teks markdown apapun di sekitarnya.
2. CEK HALAL: 
   - Deteksi bahan haram: babi, alkohol, karmin, gelatin non-spesifik.
   - Status: "Kemungkinan Halal" (jika aman), "Syubhat" (jika ambigu), atau "Berisiko" (jika jelas ada bahan haram). Jangan klaim "Halal Resmi".
3. CEK KESEHATAN & PERSONALISASI:
   - Jika gula > 20g/100g, tandai "Tinggi Gula". Jika user punya diabetes, beri peringatan keras di "personalized_message".
   - Jika sodium > 600mg/100g, tandai "Tinggi Sodium". Jika user hipertensi, beri peringatan keras.
   - Cocokkan bahan dengan alergi user. Jika ada kecocokan, ini adalah kondisi kritis.

Format JSON Output:
{
  "success": true,
  "halal_status": "Kemungkinan Halal|Syubhat|Berisiko",
  "halal_score": 80,
  "health_score": 25,
  "health_warning": ["Tinggi gula"],
  "personalized_message": "string (analisis tajam tentang kecocokan produk dengan penyakit/alergi user)",
  "recommendation": ["string"],
  "ai_confidence": "high|medium|low",
  "data_source": "{data_source}",
  "consult_nutritionist": true,
  "nutrition_estimate": {
    "sugar_g": 0, "sodium_mg": 0, "calories": 0, "carbs_g": 0, "protein_g": 0, "fat_g": 0
  }
}
PROMPT,

            'halal_check' => <<<PROMPT
Anda adalah sistem pemeriksa halal. Evaluasi produk atau bahan berikut.
User: {user_name}
Input: {user_message}

ATURAN WAJIB:
1. Langsung berikan analisis bahan. DILARANG menggunakan kata pembuka.
2. Kategorikan bahan ke dalam: Aman, Syubhat (ragu-ragu), atau Kritis (haram).
3. Berikan justifikasi singkat mengapa bahan tersebut masuk kategori tersebut.
PROMPT,

            'recommendation' => <<<PROMPT
Anda adalah konsultan gizi Halalytics.
User: {user_name} ({user_age} tahun)
Kondisi Medis: {user_diseases}
Alergi: {user_allergies}
Permintaan: {user_message}

ATURAN WAJIB:
1. Berikan 3-5 saran konkrit dan dapat dieksekusi (actionable).
2. DILARANG berbasa-basi. Langsung berikan rekomendasi.
3. Selaraskan saran dengan kondisi medis dan alergi user secara ketat.
PROMPT,

            default => 'Anda adalah asisten AI Halalytics. Jawablah instruksi berikut secara langsung tanpa basa-basi: {user_message}',
        };
    }
}
