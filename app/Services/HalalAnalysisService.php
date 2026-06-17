<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class HalalAnalysisService
{
    /**
     * Builds the prompt for Halal analysis based on ingredients and certification.
     */
    public function buildHalalPrompt(array $ingredients, ?string $halalCertNumber = null): string
    {
        $ingredientsList = implode(', ', $ingredients);
        $certInfo = $halalCertNumber 
            ? "Produk ini memiliki klaim sertifikat halal dengan nomor: {$halalCertNumber}"
            : "Produk ini TIDAK memiliki sertifikat halal resmi.";

        return <<<PROMPT
Kamu adalah sistem analisis halal otomatis yang ahli dalam hukum Islam terkait makanan dan minuman, serta familiar dengan standar MUI (Majelis Ulama Indonesia), LPPOM, dan Codex Alimentarius.

{$certInfo}

Daftar bahan produk:
{$ingredientsList}

Tugasmu:
1. Analisis SETIAP bahan satu per satu.
2. Tentukan status masing-masing bahan: HALAL, HARAM, atau SYUBHAT.
3. Tentukan verdict keseluruhan produk.
4. Berikan confidence score 0-100.

Aturan penentuan verdict:
- Jika ada 1+ bahan HARAM → verdict: HARAM
- Jika ada 1+ bahan SYUBHAT dan tidak ada sertifikat → verdict: SYUBHAT
- Jika semua bahan HALAL dan ada sertifikat valid → verdict: HALAL_CERTIFIED
- Jika semua bahan HALAL tapi tidak ada sertifikat → verdict: HALAL_UNCERTIFIED

Referensi bahan HARAM yang umum:
- Babi dan turunannya: pork, lard, ham, bacon, pepsin (babi), gelatin (jika dari babi)
- Alkohol: ethanol, ethyl alcohol, wine, beer, sake (kecuali kadar sangat kecil dari fermentasi alami)
- Darah dan turunannya: blood plasma, hemoglobin
- Bangkai, hewan tidak disembelih secara syar'i

Referensi bahan SYUBHAT:
- Gelatin (tidak jelas sumbernya)
- L-Cysteine / E920 (bisa dari rambut manusia atau bulu babi)
- Emulsifier: E471, E472a-f, E473, E474, E475 (bisa dari lemak hewan)
- Perisa / flavour / natural flavour (sumber tidak jelas)
- Carmine / E120 (dari serangga)
- Rennet (enzim keju, bisa dari hewan)
- Whey (produk susu, halal jika tidak tercampur najis)

Berikan response HANYA dalam format JSON berikut, tanpa teks tambahan apapun:

{
  "ingredients_analysis": [
    {
      "name": "nama bahan",
      "status": "HALAL|HARAM|SYUBHAT",
      "reason": "alasan singkat dalam bahasa Indonesia",
      "confidence": 85
    }
  ],
  "verdict": "HALAL_CERTIFIED|HALAL_UNCERTIFIED|SYUBHAT|HARAM",
  "verdict_reason": "penjelasan singkat mengapa verdict ini diberikan",
  "critical_ingredients": ["bahan1", "bahan2"],
  "overall_confidence": 90,
  "notes": "catatan tambahan jika ada (nullable)"
}
PROMPT;
    }
}
