<?php

namespace App\Http\Controllers\Promo;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PromoSetting;
use App\Services\ExternalHealthArticleService;

class PageController extends Controller
{
    public function __construct(
        private readonly ExternalHealthArticleService $externalArticles
    ) {
    }

    public function home()
    {
        $settings = PromoSetting::getAllSettings();
        $latestBlogs = Article::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $externalArticles = $this->externalArticles->search('', 3);
        
        // Data for specialized services
        $medicines = \App\Models\Medicine::take(6)->get();
        $diseases = \App\Models\HealthEncyclopedia::where('alphabet', 'A')->take(12)->get();

        return view('promo.home', compact('settings', 'latestBlogs', 'externalArticles', 'medicines', 'diseases'));
    }

    public function specialized($slug)
    {
        $settings = PromoSetting::getAllSettings();
        
        switch ($slug) {
            case 'diabetes':
                $data = [
                    'title' => 'Diabetes Care',
                    'desc' => 'Program manajemen gula darah terpadu dengan pemantauan AI dan konsultasi spesialis endokrin.',
                    'icon' => '🩺',
                    'color' => 'emerald'
                ];
                break;
            case 'heart':
                $data = [
                    'title' => 'Heart Health',
                    'desc' => 'Layanan pemantauan kesehatan jantung, kolesterol, dan risiko kardiovaskular secara real-time.',
                    'icon' => '🫀',
                    'color' => 'rose'
                ];
                break;
            case 'mental':
                $data = [
                    'title' => 'Mental Health Center',
                    'desc' => 'Sesi konseling aman dan privat dengan psikolog klinis untuk kesehatan mental Anda.',
                    'icon' => '🧠',
                    'color' => 'purple'
                ];
                break;
            case 'skin':
                $data = [
                    'title' => 'Haloskin - Perawatan Kulit',
                    'desc' => 'Konsultasi dermatologi, analisis kulit AI, dan rekomendasi skincare halal yang dipersonalisasi.',
                    'icon' => '✨',
                    'color' => 'pink'
                ];
                break;
            default:
                abort(404);
        }

        $data['slug'] = $slug;
        $relatedArticles = Article::where('category', 'like', "%{$slug}%")->take(3)->get();
        
        return view('promo.specialized', compact('settings', 'data', 'relatedArticles'));
    }

    public function medicineDetail($id)
    {
        $settings = PromoSetting::getAllSettings();
        $medicine = \App\Models\Medicine::where('id_medicine', $id)->firstOrFail();
        $relatedArticles = Article::where('content', 'like', "%{$medicine->generic_name}%")->take(3)->get();

        return view('promo.medicine_detail', compact('settings', 'medicine', 'relatedArticles'));
    }

    public function features()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.features', compact('settings'));
    }

    public function about()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.about', compact('settings'));
    }

    public function download()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.download', compact('settings'));
    }

    public function privacy()
    {
        $settings = PromoSetting::getAllSettings();
        return view('promo.privacy', compact('settings'));
    }

    /**
     * AI Halalytics Assistant chat endpoint (Hilda).
     */
    public function aiChat(
        \Illuminate\Http\Request $request,
        \App\Services\GeminiService $gemini,
        \App\Services\AI\PromptBuilderService $promptBuilder
    ) {
        $message = $request->input('message');
        if (!$message) {
            return response()->json(['reply' => 'Ada yang bisa Hilda bantu?']);
        }

        try {
            $roleInstructions = "Anda adalah Hilda, asisten AI resmi Halalytics. Tugas Anda adalah membantu pengguna memahami kesehatan, nutrisi, dan kehalalan produk secara akurat dan profesional.";

            // Deteksi konteks secara sederhana
            if (preg_match('/(obat|sakit|gejala|pusing|mual|demam)/i', $message)) {
                $roleInstructions .= " Fokuskan jawaban pada informasi medis yang tervalidasi. Ingatkan pengguna untuk tetap berkonsultasi dengan dokter jika gejala berlanjut.";
            } elseif (preg_match('/(halal|haram|babi|gelatin|syubhat|bpom|mui)/i', $message)) {
                $roleInstructions .= " Fokuskan jawaban pada panduan kehalalan bahan makanan dan obat-obatan sesuai standar MUI dan BPOM.";
            } elseif (preg_match('/(makan|diet|nutrisi|kalori|vitamin|gizi)/i', $message)) {
                $roleInstructions .= " Fokuskan jawaban pada tips nutrisi seimbang, kebutuhan kalori, and gaya hidup sehat.";
            }

            $finalPrompt = $promptBuilder->build('user_chat', [
                'user_name' => 'Pengguna Halalytics',
                'user_age' => 'Dewasa',
                'user_diseases' => 'Tidak disebutkan',
                'user_allergies' => 'Tidak disebutkan',
                'user_message' => $message,
            ], $roleInstructions . "\n\nPertanyaan: {user_message}\n\nJawaban Hilda:");

            $reply = $gemini->generateText($finalPrompt);

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Maaf, Hilda sedang tidak bisa merespon. Silakan coba lagi nanti.'
            ], 500);
        }
    }
}
