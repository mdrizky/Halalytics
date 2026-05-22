<?php

namespace App\Services\AI;

class IntentClassifierService
{
    private const INTENT_PATTERNS = [
        'APP_GUIDE' => [
            '/(?:\b|)(cara pakai|cara memakai|tutorial|fitur|bagaimana scan|bantuan|panduan|cara menggunakan)(?:\b|)/i',
        ],
        'HALAL_QUESTION' => [
            '/(?:\b|)(halal|haram|syubhat|kehalalan|mui|bpjph|sertifikat|babi|alkohol|gelatin|karmin|najis)(?:\b|)/i',
        ],
        'MEDICINE_QUESTION' => [
            '/(?:\b|)(obat|tablet|kapsul|dosis|efek samping|apotek|resep dokter|sirup|paracetamol|antibiotik|farmasi)(?:\b|)/i',
        ],
        'COSMETIC_QUESTION' => [
            '/(?:\b|)(skincare|kosmetik|makeup|lipstik|serum|toner|sunscreen|moisturizer|jerawat|kulit|wajah)(?:\b|)/i',
        ],
        'DIET_ADVICE' => [
            '/(?:\b|)(diet|menu|kalori|bmi|turun berat|meal plan|nutrisi|gizi|protein|karbohidrat|lemak|puasa)(?:\b|)/i',
        ],
        'PRODUCT_SCAN' => [
            '/(?:\b|)(barcode|produk ini|scan|komposisi|ingredients|bahan-bahan|kandungan|produk apa ini)(?:\b|)/i',
        ],
        'HEALTH_QUESTION' => [
            '/(?:\b|)(gejala|sakit|demam|batuk|nyeri|pusing|mual|dokter|rumah sakit|hipertensi|diabetes|alergi)(?:\b|)/i',
        ]
    ];

    public function classify(string $message): string
    {
        $text = strtolower(trim($message));
        $scores = [];

        foreach (self::INTENT_PATTERNS as $intent => $patterns) {
            $scores[$intent] = 0;
            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $text, $matches)) {
                    $scores[$intent] += count($matches[0]);
                }
            }
        }

        arsort($scores);
        
        $topIntent = array_key_first($scores);
        $topScore = $scores[$topIntent] ?? 0;

        if ($topScore > 0) {
            return $topIntent;
        }

        return 'GENERAL_HEALTH';
    }
}
