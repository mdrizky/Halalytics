<?php

namespace App\Services\AI;

class AiResponseFormatter
{
    public function format(string $intent, string $message): array
    {
        return [
            'intent' => $intent,
            'message' => trim($message),
            'ai_confidence' => 'medium',
            'disclaimer' => 'Jawaban AI bersifat edukatif, bukan diagnosis medis.',
        ];
    }
}
