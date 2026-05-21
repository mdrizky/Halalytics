<?php

namespace App\Services\AI;

class AiResponseFormatter
{
    public function format(string $intent, string $message, array $medicalRecommendation = []): array
    {
        $base = [
            'intent' => $intent,
            'message' => trim($message),
            'ai_confidence' => 'medium',
            'disclaimer' => 'Jawaban AI bersifat edukatif, bukan diagnosis medis.',
        ];

        if (!empty($medicalRecommendation)) {
            $base['medical_recommendation'] = $medicalRecommendation;
        }

        return $base;
    }
}
