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

        return view('promo.home', compact('settings', 'latestBlogs', 'externalArticles', 'medicines'));
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
     * AI Halalytics Assistant chat endpoint.
     */
    public function aiChat(\Illuminate\Http\Request $request, \App\Services\GeminiService $gemini)
    {
        $message = $request->input('message');
        if (empty($message)) {
            return response()->json(['error' => 'Pesan tidak boleh kosong.'], 400);
        }

        try {
            $systemPrompt = <<<PROMPT
Anda adalah AI Halalytics, asisten kecerdasan kesehatan, gizi, diet, obat, dan produk halal yang sangat ramah, sopan, dan cerdas.
Jawablah pertanyaan berikut dengan penjelasan yang akurat, informatif, dan praktis menggunakan bahasa Indonesia yang santun.
Fokuslah pada topik-topik kesehatan, gizi, panduan diet sehat, fungsi atau kehalalan obat-obatan, serta gaya hidup sehat halal.
Jika ditanya tentang topik di luar kesehatan atau gizi, tetaplah jawab secara bijaksana dan hubungkan dengan dampaknya terhadap kesehatan jasmani maupun rohani jika memungkinkan.

Pertanyaan pengguna: {$message}
PROMPT;

            $reply = $gemini->generateText($systemPrompt);

            return response()->json(['reply' => trim($reply)]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Halalytics Chat error: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Maaf, sistem AI Halalytics sedang sibuk. Silakan coba kirim ulang pertanyaan Anda sebentar lagi. 😊'
            ]);
        }
    }
}
