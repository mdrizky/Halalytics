<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HelpCenterController extends Controller
{
    /**
     * Daftar FAQ
     */
    public function faq()
    {
        $faqs = [
            [
                'category' => 'Panduan Pengguna',
                'icon' => 'menu_book',
                'items' => [
                    ['q' => 'Bagaimana cara scan barcode produk?', 'a' => 'Buka aplikasi → tap tombol Scan di menu bawah → arahkan kamera ke barcode produk → hasil akan muncul otomatis.'],
                    ['q' => 'Bagaimana cara mengecek status halal produk?', 'a' => 'Anda bisa scan barcode, ketik nama produk di pencarian, atau gunakan fitur Verifikasi Sertifikat Halal.'],
                    ['q' => 'Apakah data scan saya tersimpan?', 'a' => 'Ya, semua riwayat scan tersimpan di menu Riwayat. Anda bisa melihat produk yang pernah di-scan kapan saja.'],
                ],
            ],
            [
                'category' => 'Fitur Kesehatan',
                'icon' => 'favorite',
                'items' => [
                    ['q' => 'Bagaimana cara menggunakan Pengingat Obat?', 'a' => 'Masuk ke Health Suite → Pengingat Obat → Buat Pengingat Baru → Cari obat → Isi jadwal → Simpan. Anda akan menerima notifikasi sesuai jadwal.'],
                    ['q' => 'Apakah hasil kuis kesehatan mental akurat?', 'a' => 'Kuis GAD-7 dan PHQ-9 adalah alat skrining standar internasional. Hasilnya bersifat panduan awal, bukan diagnosis. Untuk diagnosis resmi, konsultasikan dengan profesional.'],
                    ['q' => 'Bagaimana AI Health Assistant bekerja?', 'a' => 'AI kami menggunakan teknologi Gemini untuk memberikan informasi kesehatan. AI mempertimbangkan profil medis Anda (jika sudah diisi) untuk jawaban yang lebih personal.'],
                ],
            ],
            [
                'category' => 'Akun & Keamanan',
                'icon' => 'security',
                'items' => [
                    ['q' => 'Bagaimana cara mengubah kata sandi?', 'a' => 'Masuk ke Pengaturan → Pengaturan Akun → Ubah Kata Sandi → Masukkan kata sandi lama dan baru → Simpan.'],
                    ['q' => 'Apakah data medis saya aman?', 'a' => 'Ya, semua data medis dienkripsi dan hanya bisa diakses oleh Anda. Kami tidak membagikan data Anda ke pihak ketiga.'],
                    ['q' => 'Bagaimana cara menghapus akun?', 'a' => 'Hubungi customer support kami melalui email atau chat untuk permintaan penghapusan akun.'],
                ],
            ],
            [
                'category' => 'Teknis',
                'icon' => 'build',
                'items' => [
                    ['q' => 'Kenapa kamera scan tidak berfungsi?', 'a' => 'Pastikan izin kamera sudah diberikan. Buka Pengaturan HP → Aplikasi → Halalytics → Izin → aktifkan Kamera.'],
                    ['q' => 'Aplikasi terasa lambat, apa yang harus dilakukan?', 'a' => 'Coba clear cache aplikasi, pastikan koneksi internet stabil, dan update ke versi terbaru.'],
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $faqs,
        ]);
    }

    /**
     * Kirim tiket bantuan
     */
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:panduan,kesehatan,akun,teknis,lainnya',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $ticket = DB::table('help_requests')->insertGetId([
            'id_user' => $request->user()->id_user,
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket bantuan berhasil dikirim. Tim kami akan merespons dalam 1x24 jam.',
            'data' => ['ticket_id' => $ticket],
        ]);
    }

    /**
     * Riwayat tiket user
     */
    public function myRequests(Request $request)
    {
        $tickets = DB::table('help_requests')
            ->where('id_user', $request->user()->id_user)
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ]);
    }
}
