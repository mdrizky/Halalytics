<?php

namespace App\Services\AI;

class IntentClassifierService
{
    public function classify(string $message): string
    {
        $text = strtolower(trim($message));

        if ($this->containsAny($text, ['cara pakai', 'cara memakai', 'tutorial', 'fitur aplikasi', 'bagaimana scan'])) {
            return 'APP_GUIDE';
        }
        if ($this->containsAny($text, ['halal', 'haram', 'syubhat', 'kehalalan', 'sertifikat mui'])) {
            return 'HALAL_QUESTION';
        }
        if ($this->containsAny($text, ['obat', 'tablet', 'kapsul', 'dosis', 'efek samping obat'])) {
            return 'MEDICINE_QUESTION';
        }
        if ($this->containsAny($text, ['skincare', 'kosmetik', 'makeup', 'lipstik'])) {
            return 'COSMETIC_QUESTION';
        }
        if ($this->containsAny($text, ['diet', 'menu', 'kalori', 'bmi', 'turun berat', 'meal plan'])) {
            return 'DIET_ADVICE';
        }
        if ($this->containsAny($text, ['barcode', 'produk ini', 'scan', 'komposisi'])) {
            return 'PRODUCT_SCAN';
        }
        if ($this->containsAny($text, ['gejala', 'sakit', 'demam', 'batuk', 'nyeri'])) {
            return 'HEALTH_QUESTION';
        }

        return 'GENERAL_HEALTH';
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
