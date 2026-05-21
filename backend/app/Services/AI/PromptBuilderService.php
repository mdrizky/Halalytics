<?php

namespace App\Services\AI;

use App\Models\User;

class PromptBuilderService
{
    public function buildPrompt(string $intent, string $userMessage, ?User $user = null): string
    {
        $languageInstruction = $this->languageInstruction($user);
        $userContext = $this->userContext($user);

        $intentPrompt = match ($intent) {
            'BMI_CALCULATION' => 'Hitung BMI jika data berat/tinggi tersedia, validasi rentang data, lalu berikan kategori BMI, target aman, dan saran praktis berbasis evidence.',
            'HEALTH_QUESTION' => 'Fokus edukasi gejala, faktor risiko, pencegahan, kapan ke dokter/IGD, dan sertakan red-flag. Jangan diagnosis final.',
            'HALAL_QUESTION' => 'Fokus status halal/syubhat/haram berbasis bahan. Jangan klaim sertifikasi resmi tanpa bukti.',
            'PRODUCT_SCAN' => 'Analisis bahan, gula/sodium/lemak, potensi syubhat, dan dampak jangka panjang berbasis evidence.',
            'DIET_ADVICE' => 'Berikan saran gizi realistis dan personal sesuai profil user.',
            'MEDICINE_QUESTION' => 'Jelaskan opsi obat yang relevan terhadap keluhan user, efek samping umum, kontraindikasi dasar, dan perhatian keamanan. Hindari dosis spesifik obat resep serta sarankan konsultasi untuk kondisi berat.',
            'APP_GUIDE' => 'Jelaskan cara memakai fitur Halalytics langkah demi langkah secara ringkas.',
            default => 'Jawab natural, membantu, dan tetap dalam domain kesehatan + halal.',
        };

        return "{$languageInstruction}\n\n" .
            "Kamu adalah AI Halalytics berbasis bukti ilmiah. Jawab pertanyaan user secara relevan, tidak generik.\n" .
            "{$intentPrompt}\n\n" .
            "{$userContext}\n" .
            "Pertanyaan user: {$userMessage}";
    }

    private function languageInstruction(?User $user): string
    {
        $lang = $user?->preferred_language ?? 'id';

        return $lang === 'en'
            ? 'CRITICAL: Respond only in clear professional English.'
            : 'PENTING: Jawab hanya dalam Bahasa Indonesia yang natural, sopan, dan profesional.';
    }

    private function userContext(?User $user): string
    {
        if (!$user) {
            return 'Profil user: tidak tersedia.';
        }

        $diseases = is_array($user->diseases ?? null) ? implode(', ', $user->diseases) : ($user->diseases ?? 'tidak ada');
        $allergies = is_array($user->allergies ?? null) ? implode(', ', $user->allergies) : ($user->allergies ?? 'tidak ada');

        return sprintf(
            'Profil user: nama=%s; usia=%s; bmi=%s; penyakit=%s; alergi=%s.',
            $user->name ?? 'Pengguna',
            (string) ($user->age ?? 'tidak diisi'),
            (string) ($user->bmi ?? 'belum diukur'),
            $diseases,
            $allergies,
        );
    }
}
