@extends('promo.layout')
@section('title', 'Kebijakan Privasi - ' . ($settings['site_name'] ?? 'Halalytics'))
@section('description', 'Kebijakan privasi Halalytics: bagaimana data pengguna dikumpulkan, digunakan, dan dilindungi.')
@section('keywords', 'kebijakan privasi, keamanan data, perlindungan data, halalytics')
@section('canonical', route('privacy'))

@section('schema')
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Kebijakan Privasi",
  "url": "{{ route('privacy') }}",
  "description": "Penjelasan penggunaan dan perlindungan data pengguna Halalytics"
}
@endsection

@section('styles')
<style>
    .privacy-hero {
        background:
            radial-gradient(900px 380px at 100% -20%, rgba(31,79,214,.20), transparent 60%),
            radial-gradient(900px 380px at 0% 0%, rgba(14,165,107,.20), transparent 58%),
            linear-gradient(180deg, #f7fbf9 0%, #ffffff 100%);
    }
    .privacy-card {
        border: 1px solid #dbe3ea;
        border-radius: 22px;
        background: #fff;
    }
</style>
@endsection

@section('content')
<section class="privacy-hero pt-24 pb-14 border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">Legal & Privacy</span>
        <h1 class="mt-5 text-4xl md:text-5xl font-extrabold text-slate-900">Kebijakan Privasi</h1>
        <p class="mt-4 text-lg text-slate-600 max-w-3xl mx-auto">
            Kami berkomitmen menjaga kerahasiaan data pengguna dan memproses data secara bertanggung jawab.
        </p>
    </div>
</section>

<section class="py-14 bg-slate-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <article class="privacy-card p-7 md:p-10 text-slate-700 leading-relaxed space-y-7">
            <p>
                Di Halalytics, keamanan data Anda adalah prioritas. Dokumen ini menjelaskan bagaimana data dikumpulkan, digunakan, dan dilindungi selama Anda menggunakan layanan kami — termasuk fitur scanner, AI HILDA, konsultasi gizi, dan donor darah.
            </p>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">1. Pengumpulan Data</h2>
                <p class="mt-2">
                    Kami mengumpulkan data yang Anda berikan langsung (nama, email, nomor telepon, preferensi diet, riwayat kesehatan, alergi, golongan darah, data keluarga), data penggunaan fitur (riwayat scan barcode, analisis bahan, konsultasi gizi, donor darah, pencarian obat), serta data perangkat dan log aktivitas untuk meningkatkan pengalaman dan akurasi layanan.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">2. Penggunaan Data</h2>
                <p class="mt-2">
                    Data digunakan untuk memberikan layanan inti Halalytics: analisis kehalalan produk via Unified Scanner, deteksi interaksi obat & identifikasi pil, skor kesehatan & tracking nutrisi, pencocokan donor darah, konsultasi dengan ahli gizi, rekomendasi resep & substitusi bahan, serta personalisasi insight AI Assistant Hilda. Kami tidak menggunakan data untuk tujuan di luar layanan tanpa persetujuan eksplisit.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">3. Penyimpanan & Proteksi</h2>
                <p class="mt-2">
                    Data disimpan di server terenkripsi dengan akses terbatas. Kami menerapkan kontrol keamanan teknis (enkripsi SSL/TLS, hashing password bcrypt, token autentikasi Sanctum) dan operasional untuk melindungi data dari akses tidak sah, perubahan, kebocoran, atau kehilangan. Data scan dan chat AI Anda bersifat privat dan tidak dibagikan ke pihak ketiga tanpa izin.
                </p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                <h3 class="text-lg font-extrabold text-amber-800">Disclaimer Penting</h3>
                <p class="text-amber-800/90 text-sm mt-2">
                    Halalytics adalah alat bantu informasi berbasis AI. Aplikasi ini tidak menggantikan diagnosis dokter, resep apoteker, konseling gizi langsung, ataupun sertifikasi halal resmi dari BPJPH/MUI atau lembaga berwenang lainnya. Selalu verifikasi informasi kritis melalui tenaga medis profesional dan otoritas sertifikasi resmi.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">4. Fitur Berbagi & Komunitas</h2>
                <p class="mt-2">
                    Fitur komunitas, pelaporan produk, dan donor darah memungkinkan interaksi antar pengguna. Data yang Anda bagikan di area publik (nama tampilan, postingan, komentar) dapat dilihat oleh pengguna lain. Kontrol visibilitas profil tersedia di pengaturan akun. Kami tidak bertanggung jawab atas konten yang dibagikan pengguna di luar kendali kami.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">5. AI & Gemini Integration</h2>
                <p class="mt-2">
                    Fitur AI Assistant Hilda menggunakan Google Gemini API untuk memproses pertanyaan Anda. Data pertanyaan dikirim ke Google untuk diproses dan tidak disimpan secara permanen oleh Google untuk pelatihan model. Jangan membagikan informasi medis pribadi yang sensitif (seperti nomor BPJS, NIK, atau detail rekam medis lengkap) melalui chat AI.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">6. Hak Pengguna</h2>
                <p class="mt-2">
                    Anda berhak mengakses, memperbarui, mengekspor, atau menghapus data akun kapan saja melalui pengaturan profil. Permintaan penghapusan data diproses dalam 14 hari kerja. Hubungi tim dukungan kami jika membutuhkan bantuan terkait data pribadi Anda.
                </p>
            </div>
        </article>
    </div>
</section>

<section class="py-14 bg-white border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="privacy-card p-7 md:p-10">
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Pertanyaan Terkait Privasi?</h2>
            <p class="text-slate-600 mt-2">
                Kirimkan pesan ke tim kami. Kami akan merespons secepat mungkin.
            </p>

            <form action="{{ route('contact.send') }}" method="POST" class="mt-7 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Subjek</label>
                    <input type="text" name="subject" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Pesan</label>
                    <textarea name="message" rows="4" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"></textarea>
                </div>
                <button type="submit" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
