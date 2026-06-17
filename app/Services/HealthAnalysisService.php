<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class HealthAnalysisService
{
    /**
     * Builds the prompt for Health analysis based on nutrition data and user profile.
     */
    public function buildHealthPrompt(array $nutrition, array $userProfile): string
    {
        $productName   = $nutrition['product_name'] ?? 'Produk tidak diketahui';
        $servingSize   = $nutrition['serving_size'] ?? '100g';
        $calories      = $nutrition['calories'] ?? 0;
        $sugar         = $nutrition['sugar'] ?? 0;
        $fat           = $nutrition['fat'] ?? 0;
        $saturatedFat  = $nutrition['saturated_fat'] ?? 0;
        $sodium        = $nutrition['sodium'] ?? 0;
        $protein       = $nutrition['protein'] ?? 0;
        $fiber         = $nutrition['fiber'] ?? 0;
        $carbs         = $nutrition['carbohydrates'] ?? 0;

        $age            = $userProfile['age'] ?? 'tidak diketahui';
        $weight         = $userProfile['weight_kg'] ?? 'tidak diketahui';
        $height         = $userProfile['height_cm'] ?? 'tidak diketahui';
        $gender         = $userProfile['gender'] ?? 'tidak diketahui';
        $conditions     = implode(', ', $userProfile['health_conditions'] ?? ['tidak ada']);
        $goal           = $userProfile['diet_goal'] ?? 'maintain';
        $activityLevel  = $userProfile['activity_level'] ?? 'sedentary';

        // Hitung BMI otomatis jika data lengkap
        $bmi = '-';
        if (is_numeric($weight) && is_numeric($height) && $height > 0) {
            $bmi = round($weight / pow($height / 100, 2), 1);
        }

        return <<<PROMPT
Kamu adalah ahli gizi dan nutrisi bersertifikat yang memberikan analisis kesehatan personal berdasarkan profil pengguna dan kandungan nutrisi produk makanan/minuman.

=== PROFIL PENGGUNA ===
- Usia: {$age} tahun
- Jenis kelamin: {$gender}
- Berat badan: {$weight} kg
- Tinggi badan: {$height} cm
- BMI: {$bmi}
- Kondisi kesehatan: {$conditions}
- Tujuan diet: {$goal}
- Tingkat aktivitas: {$activityLevel}

=== DATA NUTRISI PRODUK ===
Nama produk: {$productName}
Per sajian ({$servingSize}):
- Kalori: {$calories} kkal
- Karbohidrat: {$carbs} g
- Gula: {$sugar} g
- Lemak total: {$fat} g
- Lemak jenuh: {$saturatedFat} g
- Protein: {$protein} g
- Serat: {$fiber} g
- Sodium: {$sodium} mg

=== TUGASMU ===

1. Hitung Nutri Score (A-E) menggunakan metodologi resmi:
   - Poin negatif: kalori tinggi, gula, lemak jenuh, sodium
   - Poin positif: protein, serat, buah/sayur (asumsikan 0% jika tidak ada info)
   - A = 0-2 poin, B = 3-6, C = 7-10, D = 11-18, E = 19+

2. Analisis risiko kesehatan SPESIFIK untuk profil user ini.
   Contoh: jika user punya diabetes → fokus ke gula dan indeks glikemik.
   Jika hipertensi → fokus ke sodium.
   Jika diet → fokus ke kalori dan lemak.

3. Tentukan porsi aman berdasarkan kebutuhan kalori harian user.
   Kebutuhan kalori harian estimasi: gunakan Harris-Benedict dengan activity factor.

4. Berikan 2-3 rekomendasi produk alternatif yang lebih sehat (kategori serupa).

5. Berikan verdict konsumsi: AMAN / PERHATIAN / HINDARI

Berikan response HANYA dalam format JSON berikut, tanpa teks tambahan:

{
  "nutri_score": "A|B|C|D|E",
  "nutri_score_points": 12,
  "nutri_score_breakdown": {
    "negative_points": 15,
    "positive_points": 3,
    "details": "penjelasan singkat perhitungan"
  },
  "daily_calorie_need": 2100,
  "calorie_percentage": 14.5,
  "health_risks": [
    {
      "condition": "diabetes",
      "risk_level": "TINGGI|SEDANG|RENDAH",
      "reason": "kandungan gula 18g per sajian melampaui batas aman penderita diabetes"
    }
  ],
  "safe_portion": {
    "amount": "1 sajian (30g)",
    "frequency": "maksimal 2x seminggu",
    "reason": "kandungan sodium tinggi tidak aman dikonsumsi harian"
  },
  "consumption_verdict": "AMAN|PERHATIAN|HINDARI",
  "verdict_message": "pesan singkat dalam bahasa Indonesia yang ramah dan tidak menghakimi",
  "recommendations": [
    {
      "name": "nama produk alternatif",
      "reason": "mengapa lebih sehat"
    }
  ],
  "nutritionist_note": "catatan penting khusus untuk kondisi user ini, 2-3 kalimat"
}
PROMPT;
    }
}
