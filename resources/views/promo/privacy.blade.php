@extends('promo.layout')
@section('title', 'Kebijakan Privasi - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', 'Kebijakan privasi HalalScan AI: bagaimana data pengguna dikumpulkan, digunakan, dan dilindungi.')
@section('keywords', 'kebijakan privasi, keamanan data, perlindungan data, halalscan ai')
@section('canonical', route('privacy'))

@section('schema')
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Kebijakan Privasi",
  "url": "{{ route('privacy') }}",
  "description": "Penjelasan penggunaan dan perlindungan data pengguna HalalScan AI"
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
                Di HalalScan AI, keamanan data Anda adalah prioritas. Dokumen ini menjelaskan bagaimana data dikumpulkan, digunakan, dan dilindungi selama Anda menggunakan layanan kami.
            </p>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">1. Pengumpulan Data</h2>
                <p class="mt-2">
                    Kami mengumpulkan data yang Anda berikan langsung (misalnya nama, email, preferensi kesehatan), serta data penggunaan aplikasi seperti riwayat scan produk untuk meningkatkan kualitas rekomendasi.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">2. Penggunaan Data</h2>
                <p class="mt-2">
                    Data digunakan untuk mempersonalisasi analisis halal, peringatan interaksi obat, dan insight kesehatan. Kami tidak menggunakan data pribadi untuk tujuan yang tidak relevan dengan layanan inti tanpa persetujuan.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">3. Penyimpanan & Proteksi</h2>
                <p class="mt-2">
                    Kami menerapkan kontrol keamanan teknis dan operasional untuk melindungi data dari akses tidak sah, perubahan, atau kehilangan.
                </p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                <h3 class="text-lg font-extrabold text-amber-800">Disclaimer Penting</h3>
                <p class="text-amber-800/90 text-sm mt-2">
                    HalalScan AI adalah alat bantu informasi. Aplikasi ini tidak menggantikan diagnosis dokter, apoteker, ahli gizi, ataupun sertifikasi halal resmi lembaga berwenang.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">4. Hak Pengguna</h2>
                <p class="mt-2">
                    Anda dapat meminta pembaruan atau penghapusan data akun sesuai kebijakan yang berlaku. Hubungi tim kami jika membutuhkan bantuan lebih lanjut.
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
