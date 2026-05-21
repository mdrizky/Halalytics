<?php

namespace App\Services\AI;

class IntentClassifierService
{
    public function classify(string $message): string
    {
        $msg = mb_strtolower(trim($message));

        if (preg_match('/^\d{8,13}$/', $msg)) return 'PRODUCT_SCAN';

        return match (true) {
            str_contains($msg, 'halal'), str_contains($msg, 'haram'), str_contains($msg, 'syubhat') => 'HALAL_QUESTION',
            str_contains($msg, 'obat'), str_contains($msg, 'efek samping') => 'MEDICINE_QUESTION',
            str_contains($msg, 'diet'), str_contains($msg, 'kalori'), str_contains($msg, 'gizi') => 'DIET_ADVICE',
            str_contains($msg, 'gejala'), str_contains($msg, 'penyakit'), str_contains($msg, 'tbc') => 'HEALTH_QUESTION',
            str_contains($msg, 'cara pakai'), str_contains($msg, 'fitur'), str_contains($msg, 'aplikasi') => 'APP_GUIDE',
            default => 'GENERAL_HEALTH',
        };
    }
}
