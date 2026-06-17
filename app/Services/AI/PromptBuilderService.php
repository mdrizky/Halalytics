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
Anda adalah sistem analis pakar nutrisi dan auditor halal Halalytics. Misi Anda adalah membedah profil produk secara tegas, tanpa keraguan.

Data User (Kondisi Medis Sangat Penting!):
- Nama: {user_name} ({user_age} tahun)
- Penyakit/Keluhan: {user_diseases}
- Alergi: {user_allergies}

Data Produk:
- Nama: {product_name}
- Kategori: {product_category}
- Komposisi Kimia & Umum: {ingredients_text}
- Deteksi Database Haram (Admin): {admin_haram_matches}
- Nutrisi Tersedia (per 100g): Gula={sugars}g | Sodium={sodium}mg | Lemak={fat}g | Protein={protein}g | Kalori={calories}kcal

ATURAN WAJIB & MUTLAK:
1. JSON ONLY: HANYA kembalikan JSON murni. DILARANG mereturn teks markdown, kode backtick, atau teks awalan apapun.
2. KEPASTIAN HALAL:
   - DILARANG menggunakan kata "Syubhat" atau keraguan. Status HARUS: "Halal" atau "Haram".
   - Jika field Deteksi Database Haram (Admin) menyebutkan ada bahan haram, status mutlak HARAM. Jelaskan alasannya.
   - Jika terdapat istilah bahan kimia, terjemahkan apa maksudnya ke bahasa awam di field chemical_translation (contoh: E120 = Karmin, ekstrak serangga merah).
3. KEPASTIAN SEHAT & ESTIMASI:
   - Status kesehatan HARUS: "Sehat" atau "Tidak Sehat".
   - Jika Nutrisi Tersedia bernilai 0 atau kosong, Anda WAJIB MENGUKUR/MENG-ESTIMASI kandungan nutrisi ke dalam nutrition_estimate secara logis berdasarkan urutan komposisi.
4. ANALISIS PER BAHAN (INGREDIENTS):
   - Untuk SETIAP bahan dalam komposisi, berikan analisis di field "ingredients" sebagai array of objects.
   - Setiap object bahan HARUS memiliki: ingredient_name, status ("halal"/"syubhat"/"haram"), warning (pesan peringatan dalam bahasa Indonesia), e_code (nomor E jika ada, null jika tidak), category (jenis bahan), source_type ("ingredient"/"additive"/"e_number").
   - Bahan haram: warning ditulis MERAH BOLD (contoh: "⚠️ MENGANDUNG BAHAN HARAM: Gelatin yang tidak diketahui sumbernya"). Bahan syubhat: warning ORANGE (contoh: "⚡ BAHAN SYUBHAT: E471 - Pengemulsi, perlu sertifikat halal").
5. EFEK SAMPING & PERSONALISASI ALERGI:
   - Wajib melakukan pengecekan antara Komposisi vs Alergi/Penyakit User. 
   - Tuliskan kemungkinan efek samping jangka pendek di short_term_effects (0-3 jam setelah konsumsi) dan efek jangka panjang di long_term_effects (konsumsi rutin).
   - Berikan pesan personal di personalized_message (contoh: "Halo {user_name}, produk ini harus Anda hindari karena mengandung {alergen} yang memicu reaksi pada Anda.").
6. ALTERNATIF CERDAS SEJENIS:
   - Jika produk dianalisis Haram ATAU Tidak Sehat, berikan 2 alternatif merek produk nyata yang halal dan lebih sehat.
   - WAJIB berikan alternatif dari KATEGORI YANG SAMA persis ({product_category}). Jangan merekomendasikan obat jika user mencari minuman.

Format JSON Output Wajib (Isi sesuai tipe data):
{
  "success": true,
  "halal_status": "Halal" atau "Haram",
  "halal_score": 90,
  "health_status": "Sehat" atau "Tidak Sehat",
  "health_score": 85,
  "health_warning": ["Peringatan 1"],
  "ingredients": [
    {"ingredient_name": "Gula", "status": "halal", "warning": null, "e_code": null, "category": "pemanis", "source_type": "ingredient"},
    {"ingredient_name": "Gelatin", "status": "syubhat", "warning": "⚡ BAHAN SYUBHAT: Gelatin, perlu dipastikan sumber halal", "e_code": null, "category": "pengental", "source_type": "ingredient"}
  ],
  "short_term_effects": ["Efek jangka pendek 1 (0-3 jam)", "Efek jangka pendek 2"],
  "long_term_effects": ["Efek jangka panjang karena konsumsi rutin 1", "Efek jangka panjang 2"],
  "chemical_translation": ["Bahan Kimia 1 = Artinya", "Bahan Kimia 2 = Artinya"],
  "personalized_message": "string penjelasan detail",
  "alternatives": ["Nama Produk Alternatif 1", "Nama Produk Alternatif 2"],
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
