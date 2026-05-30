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
        
        // Get user statistics for "Ketersediaan Pakar" section replacement
        $stats = [
            'total_users' => \App\Models\User::count(),
            'active_doctors' => \App\Models\User::where('role', 'ahli_gizi')->count(),
        ];

        return view('promo.home', compact('settings', 'latestBlogs', 'externalArticles', 'medicines', 'diseases', 'stats'));
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
            return response()->json(['reply' => 'Halo! Ada yang bisa Hilda bantu mengenai kesehatan, nutrisi, atau kehalalan produk?']);
        }

        try {
            // Instruksi yang lebih mendalam agar jawaban tidak kaku/template
            $roleInstructions = "Anda adalah Hilda, asisten AI resmi Halalytics. Jawablah dengan nada yang ramah, profesional, dan sangat informatif. Gunakan format markdown (bold, list) agar mudah dibaca. 
            
            Prinsip jawaban Anda:
            1. Jika mengenai kesehatan: Berikan penjelasan medis yang mudah dimengerti namun mendalam. Berikan tips gaya hidup sehat yang praktis.
            2. Jika mengenai kehalalan: Jelaskan berdasarkan standar MUI/BPJPH. Berikan informasi tentang titik kritis bahan jika relevan.
            3. Jika mengenai obat: Jelaskan fungsi umum dan ingatkan pentingnya resep dokter untuk obat keras.
            
            HINDARI jawaban singkat yang terasa seperti bot. Berikan konteks tambahan yang bermanfaat bagi pengguna.";

            // Deteksi konteks secara dinamis untuk menyesuaikan gaya bahasa
            if (preg_match('/(obat|sakit|gejala|pusing|mual|demam|virus|bakteri|infeksi)/i', $message)) {
                $roleInstructions .= " Fokuskan jawaban pada edukasi medis. Berikan langkah-langkah P3K atau pertolongan pertama jika memungkinkan.";
            } elseif (preg_match('/(halal|haram|babi|gelatin|syubhat|bpom|mui|alkohol|emulsifier|lecithin)/i', $message)) {
                $roleInstructions .= " Fokuskan pada edukasi kehalalan bahan pangan dan kosmetik. Jelaskan mengapa suatu bahan dianggap syubhat atau haram.";
            } elseif (preg_match('/(makan|diet|nutrisi|kalori|vitamin|gizi|protein|karbohidrat|lemak)/i', $message)) {
                $roleInstructions .= " Fokuskan pada panduan gizi seimbang dan manajemen berat badan yang sehat.";
            }

            $finalPrompt = $promptBuilder->build('user_chat', [
                'user_name' => 'Pengguna Halalytics',
                'user_message' => $message,
            ], $roleInstructions . "\n\nPertanyaan Pengguna: {user_message}\n\nJawaban Detail Hilda:");

            // Tingkatkan temperature sedikit agar lebih kreatif (0.8) dan max tokens lebih besar
            $reply = $gemini->generateText($finalPrompt, 0.8, 3072);

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Maaf, Hilda sedang beristirahat sejenak untuk memperbarui sistem. Silakan coba sapa Hilda lagi beberapa saat lagi ya!'
            ], 500);
        }
    }
}
