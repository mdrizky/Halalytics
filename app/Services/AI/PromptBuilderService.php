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
    public function build(string $type, array $variables = [], ?string $fallbackTemplate = null): string
    {
        $featureKey = self::TYPE_MAP[$type] ?? $type;
        $record = AiPrompt::forFeature($featureKey);

        $template = $record?->system_prompt
            ?? $fallbackTemplate
            ?? $this->defaultTemplate($type);

        if ($record && $record->user_prompt_template) {
            $userPart = $record->buildUserPrompt($variables);
            if (trim($userPart) !== '') {
                $template = trim($template) . "\n\n" . $userPart;
            }
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
Kamu adalah AI Halalytics — asisten kesehatan dan halal berbasis bukti ilmiah.
Kamu WAJIB menjawab setiap pertanyaan dengan JELAS, INFORMATIF, dan PERSONAL.
JANGAN pernah menjawab dengan kalimat placeholder atau tidak menjawab pertanyaan.
Nama user: {user_name}
Umur: {user_age} tahun
Penyakit: {user_diseases}
Alergi: {user_allergies}

Pertanyaan user: {user_message}

Jawab dengan bahasa Indonesia yang ramah, informatif, dan berbasis fakta ilmiah.
Jika pertanyaan tentang halal, berikan analisis berdasarkan komposisi.
Jika pertanyaan tentang kesehatan, berikan saran berbasis evidence.
Selalu sertakan disclaimer untuk konsultasi dokter/ahli gizi jika relevan.
PROMPT,

            'food_analysis' => <<<PROMPT
Kamu adalah AI Halalytics — asisten kesehatan dan halal berbasis bukti ilmiah.
Kamu WAJIB menganalisis produk ini secara LENGKAP, JELAS, dan PERSONAL.
JANGAN pernah menjawab dengan kalimat placeholder.

Data User:
- Nama: {user_name}
- Umur: {user_age} tahun
- Penyakit: {user_diseases}
- Alergi: {user_allergies}
- Scan minggu ini: {weekly_scan_summary}
- Produk tinggi gula yang discan: {high_sugar_scan_count} kali

Data Produk:
- Nama: {product_name}
- Barcode: {barcode}
- Kategori: {product_category}
- Komposisi: {ingredients_text}
- Nutrisi per 100g: Gula={sugars}g | Sodium={sodium}mg | Lemak={fat}g | Protein={protein}g | Kalori={calories}kcal
- Label halal: {halal_label}
- Sumber data: {data_source}

Aturan Analisis WAJIB:
1. CEK HALAL: Identifikasi bahan haram (babi, alkohol, gelatin non-halal, karmin, dll).
   - Tidak ada bahan jelas haram → "Kemungkinan Halal (AI Analysis)"
   - Ada bahan syubhat → "Syubhat — perlu verifikasi"
   - Ada bahan haram → "Berisiko — terindikasi bahan haram"
   - JANGAN klaim "Halal Resmi" kecuali ada sertifikasi MUI/BPJPH.

2. CEK KESEHATAN:
   - Gula > 20g/100g → "Tinggi Gula"
   - Sodium > 600mg/100g → "Tinggi Sodium"
   - Protein < 3g && Gula > 20g → "Kalori Kosong"
   - Ingredient pertama = gula → "Gula sebagai bahan dominan"

3. CEK PERSONALISASI:
   - Jika user diabetes && produk tinggi gula → beri warning KERAS
   - Jika user hipertensi && sodium tinggi → beri warning hipertensi
   - Jika user obesitas && produk tinggi gula → beri warning lebih serius
   - Jika user alergi && ada bahan alergen → beri WARNING ALERGI MERAH

4. EFEK JANGKA PANJANG (berbasis fakta ilmiah, bukan asumsi):
   - Gunakan: "Konsumsi berlebihan dalam jangka panjang dapat meningkatkan risiko..."
   - Sumber: WHO, FDA, BPOM, jurnal ilmiah

Jawab HANYA dalam format JSON berikut:
{
  "success": true,
  "halal_status": "Kemungkinan Halal|Syubhat|Haram",
  "halal_score": 80,
  "health_score": 25,
  "health_warning": [
    "Tinggi gula"
  ],
  "personalized_message": "string — pesan personal berdasarkan profil user",
  "recommendation": [
    "Batasi konsumsi"
  ],
  "ai_confidence": "high|medium|low",
  "data_source": "string",
  "consult_nutritionist": true,
  "nutrition_estimate": {
    "sugar_g": 0,
    "sodium_mg": 0,
    "calories": 0,
    "carbs_g": 0,
    "protein_g": 0,
    "fat_g": 0
  }
}
PROMPT,

            'halal_check' => <<<PROMPT
Kamu adalah AI Halalytics — spesialis analisis kehalalan produk.
Analisis status halal dari pertanyaan atau produk berikut.
Berikan jawaban yang jelas, berbasis fakta, dan tidak mengklaim "Halal Resmi" tanpa sertifikasi.

User: {user_name}
Pertanyaan/Produk: {user_message}

Jawab dalam bahasa Indonesia yang jelas dan informatif.
PROMPT,

            'recommendation' => <<<PROMPT
Kamu adalah AI Halalytics — ahli gizi dan kesehatan halal.
Berikan rekomendasi diet dan gaya hidup yang personal berdasarkan profil user.

User: {user_name}, {user_age} tahun
Kondisi: {user_diseases}
Alergi: {user_allergies}
Pertanyaan: {user_message}

Berikan saran yang praktis, berbasis evidence, dan sesuai kondisi user.
PROMPT,

            default => 'Kamu adalah AI Halalytics — asisten kesehatan dan halal berbasis bukti ilmiah. Jawab dengan jelas, personal, dan informatif. Jangan gunakan kalimat placeholder.',
        };
    }
}
