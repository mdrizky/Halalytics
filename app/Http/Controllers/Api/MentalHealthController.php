<?php

namespace App\Http\Controllers\Api;

use App\Models\MentalHealthQuizResult;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MentalHealthController extends Controller
{
    /**
     * Simpan hasil kuis kesehatan mental
     */
    public function submitQuiz(Request $request)
    {
        $validated = $request->validate([
            'quiz_type' => 'required|in:gad7,phq9,dass21',
            'answers' => 'required|array',
        ]);

        $totalScore = array_sum($validated['answers']);

        $severityLevel = match ($validated['quiz_type']) {
            'gad7' => MentalHealthQuizResult::interpretGad7($totalScore),
            'phq9' => MentalHealthQuizResult::interpretPhq9($totalScore),
            default => 'unknown',
        };

        // Generate AI recommendation
        $aiRecommendation = null;
        try {
            $gemini = app(GeminiService::class);
            $quizName = $validated['quiz_type'] === 'gad7' ? 'GAD-7 (Kecemasan)' : 'PHQ-9 (Depresi)';
            $prompt = "Seorang user baru saja mengisi kuis kesehatan mental {$quizName} dengan skor total {$totalScore} (tingkat: {$severityLevel}). "
                . "Berikan rekomendasi singkat dalam 3-4 paragraf dalam Bahasa Indonesia yang ramah dan supportif. "
                . "Jangan diagnosis. Sarankan konsultasi profesional jika skor moderate/severe. "
                . "Berikan juga 3 tips self-care yang bisa langsung dilakukan.";
            $aiRecommendation = $gemini->generateText($prompt);
        } catch (\Exception $e) {
            // Fallback recommendation
            $aiRecommendation = $this->getFallbackRecommendation($severityLevel);
        }

        $result = MentalHealthQuizResult::create([
            'id_user' => $request->user()->id_user,
            'quiz_type' => $validated['quiz_type'],
            'total_score' => $totalScore,
            'severity_level' => $severityLevel,
            'answers' => $validated['answers'],
            'ai_recommendation' => $aiRecommendation,
        ]);

        $severityLabels = [
            'minimal' => 'Minimal',
            'mild' => 'Ringan',
            'moderate' => 'Sedang',
            'moderately_severe' => 'Cukup Berat',
            'severe' => 'Berat',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $result->id,
                'quiz_type' => $result->quiz_type,
                'total_score' => $result->total_score,
                'severity_level' => $result->severity_level,
                'severity_label' => $severityLabels[$result->severity_level] ?? $result->severity_level,
                'recommendation' => $result->ai_recommendation,
                'created_at' => $result->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Ambil riwayat kuis
     */
    public function history(Request $request)
    {
        $results = MentalHealthQuizResult::where('id_user', $request->user()->id_user)
            ->orderByDesc('created_at')
            ->take(20)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'quiz_type' => $r->quiz_type,
                'total_score' => $r->total_score,
                'severity_level' => $r->severity_level,
                'created_at' => $r->created_at->toISOString(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Dapatkan pertanyaan kuis
     */
    public function getQuestions(string $type)
    {
        $questions = match ($type) {
            'gad7' => [
                ['id' => 'q1', 'text' => 'Merasa gugup, cemas, atau gelisah'],
                ['id' => 'q2', 'text' => 'Tidak mampu menghentikan atau mengendalikan rasa khawatir'],
                ['id' => 'q3', 'text' => 'Terlalu khawatir tentang berbagai hal'],
                ['id' => 'q4', 'text' => 'Sulit untuk rileks'],
                ['id' => 'q5', 'text' => 'Sangat gelisah sehingga sulit untuk duduk diam'],
                ['id' => 'q6', 'text' => 'Mudah kesal atau mudah tersinggung'],
                ['id' => 'q7', 'text' => 'Merasa takut seolah-olah sesuatu yang buruk akan terjadi'],
            ],
            'phq9' => [
                ['id' => 'q1', 'text' => 'Kurang tertarik atau kurang berminat melakukan sesuatu'],
                ['id' => 'q2', 'text' => 'Merasa sedih, murung, atau putus asa'],
                ['id' => 'q3', 'text' => 'Sulit tidur atau tidur terlalu banyak'],
                ['id' => 'q4', 'text' => 'Merasa lelah atau kurang bertenaga'],
                ['id' => 'q5', 'text' => 'Kurang nafsu makan atau makan terlalu banyak'],
                ['id' => 'q6', 'text' => 'Merasa buruk tentang diri sendiri'],
                ['id' => 'q7', 'text' => 'Sulit berkonsentrasi pada sesuatu'],
                ['id' => 'q8', 'text' => 'Bergerak atau berbicara sangat lambat, atau sebaliknya, sangat gelisah'],
                ['id' => 'q9', 'text' => 'Pikiran bahwa lebih baik mati atau menyakiti diri sendiri'],
            ],
            default => [],
        };

        $options = [
            ['value' => 0, 'label' => 'Tidak sama sekali'],
            ['value' => 1, 'label' => 'Beberapa hari'],
            ['value' => 2, 'label' => 'Lebih dari separuh waktu'],
            ['value' => 3, 'label' => 'Hampir setiap hari'],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'quiz_type' => $type,
                'title' => $type === 'gad7' ? 'Tes Kecemasan (GAD-7)' : 'Tes Depresi (PHQ-9)',
                'instruction' => 'Dalam 2 minggu terakhir, seberapa sering Anda merasa terganggu oleh hal-hal berikut:',
                'questions' => $questions,
                'options' => $options,
            ],
        ]);
    }

    private function getFallbackRecommendation(string $severity): string
    {
        return match ($severity) {
            'minimal' => "Hasil kuis menunjukkan tingkat yang minimal. Ini adalah tanda positif! Tetap jaga kesehatan mental Anda dengan:\n\n1. Istirahat yang cukup (7-8 jam per malam)\n2. Olahraga rutin minimal 30 menit per hari\n3. Luangkan waktu untuk hobi yang Anda sukai",
            'mild' => "Hasil kuis menunjukkan tingkat ringan. Beberapa langkah yang bisa membantu:\n\n1. Praktikkan teknik pernapasan dalam saat merasa cemas\n2. Bicarakan perasaan Anda dengan orang terdekat\n3. Coba meditasi atau mindfulness 10 menit sehari\n\nJika keluhan berlanjut, pertimbangkan untuk berkonsultasi dengan psikolog.",
            'moderate' => "Hasil kuis menunjukkan tingkat sedang. Kami menyarankan Anda untuk:\n\n1. Segera bicara dengan orang yang Anda percaya tentang perasaan Anda\n2. Pertimbangkan untuk berkonsultasi dengan psikolog atau psikiater\n3. Jaga pola tidur dan makan yang teratur\n\nAnda tidak sendirian. Bantuan profesional sangat direkomendasikan.",
            default => "Hasil kuis menunjukkan bahwa Anda mungkin membutuhkan bantuan profesional. Kami sangat menyarankan untuk:\n\n1. Segera hubungi psikolog atau psikiater\n2. Hubungi hotline kesehatan mental: 119 ext. 8\n3. Jangan ragu untuk meminta bantuan\n\nAnda layak mendapatkan dukungan. Bantuan profesional bisa membuat perbedaan besar.",
        };
    }

    /**
     * Get mental health topics
     */
    public function topics()
    {
        $topics = [
            ['id' => 1, 'name' => 'Kecemasan', 'icon' => 'psychology', 'color' => '#4CAF50', 'description' => 'Mengelola rasa cemas dan khawatir berlebihan'],
            ['id' => 2, 'name' => 'Depresi', 'icon' => 'mood_bad', 'color' => '#2196F3', 'description' => 'Memahami dan mengatasi perasaan sedih berkepanjangan'],
            ['id' => 3, 'name' => 'Stres', 'icon' => 'local_fire_department', 'color' => '#FF9800', 'description' => 'Teknik mengelola stres dalam kehidupan sehari-hari'],
            ['id' => 4, 'name' => 'Tidur', 'icon' => 'bedtime', 'color' => '#9C27B0', 'description' => 'Meningkatkan kualitas tidur untuk kesehatan mental'],
            ['id' => 5, 'name' => 'Mindfulness', 'icon' => 'self_improvement', 'color' => '#00BCD4', 'description' => 'Praktik kesadaran untuk ketenangan batin'],
            ['id' => 6, 'name' => 'Relasi', 'icon' => 'people', 'color' => '#E91E63', 'description' => 'Membangun hubungan yang sehat dan supportif'],
        ];

        return response()->json([
            'success' => true,
            'data' => $topics,
        ]);
    }

    /**
     * Get mental health articles
     */
    public function articles()
    {
        $articles = [
            [
                'id' => 1,
                'title' => '5 Cara Mengelola Kecemasan Sehari-hari',
                'summary' => 'Teknik sederhana yang bisa Anda lakukan kapan saja untuk mengurangi rasa cemas.',
                'category' => 'Kecemasan',
                'read_time' => '5 menit',
                'image_url' => null,
                'created_at' => now()->subDays(1)->toISOString(),
            ],
            [
                'id' => 2,
                'title' => 'Mengenal Tanda-tanda Burnout',
                'summary' => 'Pelajari gejala burnout dan cara mengatasinya sebelum terlambat.',
                'category' => 'Stres',
                'read_time' => '7 menit',
                'image_url' => null,
                'created_at' => now()->subDays(3)->toISOString(),
            ],
            [
                'id' => 3,
                'title' => 'Meditasi untuk Pemula: Panduan Lengkap',
                'summary' => 'Mulai perjalanan meditasi Anda dengan panduan langkah demi langkah.',
                'category' => 'Mindfulness',
                'read_time' => '10 menit',
                'image_url' => null,
                'created_at' => now()->subDays(5)->toISOString(),
            ],
            [
                'id' => 4,
                'title' => 'Tips Tidur Nyenyak di Malam Hari',
                'summary' => 'Kebiasaan sebelum tidur yang dapat meningkatkan kualitas istirahat Anda.',
                'category' => 'Tidur',
                'read_time' => '4 menit',
                'image_url' => null,
                'created_at' => now()->subDays(7)->toISOString(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get available mental health experts
     */
    public function experts()
    {
        $experts = \App\Models\Specialist::where('is_available', true)
            ->orderByDesc('rating')
            ->limit(20)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'specialty' => $s->specialty,
                    'avatar_url' => $s->avatar_url,
                    'bio' => $s->bio,
                    'is_online' => $s->is_online,
                    'rating' => (float) $s->rating,
                    'total_consultations' => $s->total_consultations,
                ];
            });

        // If no real specialists exist, return sample data
        if ($experts->isEmpty()) {
            $experts = collect([
                ['id' => 1, 'name' => 'Dr. Siti Aminah, M.Psi', 'specialty' => 'Psikologi Klinis', 'avatar_url' => null, 'bio' => 'Spesialis gangguan kecemasan dan depresi', 'is_online' => true, 'rating' => 4.9, 'total_consultations' => 150],
                ['id' => 2, 'name' => 'Dr. Ahmad Rizki, Sp.KJ', 'specialty' => 'Psikiater', 'avatar_url' => null, 'bio' => 'Berpengalaman 10+ tahun dalam kesehatan mental', 'is_online' => false, 'rating' => 4.8, 'total_consultations' => 200],
                ['id' => 3, 'name' => 'Sarah Nurul, M.Psi', 'specialty' => 'Konselor', 'avatar_url' => null, 'bio' => 'Spesialis terapi kognitif perilaku', 'is_online' => true, 'rating' => 4.7, 'total_consultations' => 95],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $experts,
        ]);
    }

    /**
     * Request consultation with expert
     */
    public function requestExpert(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|integer',
            'topic' => 'nullable|string|max:200',
        ]);

        try {
            $session = \App\Models\ConsultationSession::create([
                'user_id' => $request->user()->id_user,
                'specialist_id' => $request->specialist_id,
                'status' => 'waiting',
                'topic' => $request->topic,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan konsultasi berhasil dikirim. Ahli akan segera merespons.',
                'data' => [
                    'session_id' => $session->id,
                    'status' => 'waiting',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim permintaan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
