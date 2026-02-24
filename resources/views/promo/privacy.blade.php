@extends('promo.layout')
@section('title', 'Kebijakan Privasi - ' . ($settings['site_name'] ?? 'HalalScan AI'))

@section('content')
<div class="py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-8">Kebijakan Privasi</h1>
        
        <div class="prose prose-green max-w-none text-gray-600 space-y-6">
            <p>Di HalalScan AI, keamanan data Anda adalah prioritas kami. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.</p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-4">1. Pengumpulan Data</h3>
            <p>Kami mengumpulkan informasi yang Anda berikan secara langsung saat Anda mendaftar akun, mengatur profil kesehatan (umur, alergi, golongan darah), serta riwayat scan produk. Data ini kami simpan terenkripsi dengan aman di peladen kami.</p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-4">2. Penggunaan Data</h3>
            <p>Data profil kesehatan dan pantangan Anda HANYA digunakan oleh AI untuk memberikan peringatan dan rekomendasi produk yang relevan untuk interaksi obat dan tingkat kesehatan personal Anda.</p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-4">3. Disclaimer (Penyangkalan Hukum)</h3>
            <div class="bg-gray-100 p-6 rounded-xl border border-gray-200">
                <p class="text-sm font-semibold text-gray-800">Harap dicatat:</p>
                <p class="text-sm">Aplikasi ini dirancang sebagai instrumen bantu dan asisten cerdas berbasikan AI. Kami TIDAK menggantikan diagnosis dokter, ahli gizi profesional, apoteker, atau sertifikasi halal MUI yang resmi. Selalu periksa label dengan dokter Anda bila menghadapi kondisi medis yang berisiko.</p>
            </div>
            
            <hr class="my-12">

            <!-- Formulir Kontak Publik -->
            <div class="bg-blue-50 rounded-2xl p-8 border border-blue-100 mt-12">
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Ada Pertanyaan? Hubungi Kami</h3>
                <p class="text-blue-700 mb-6">Jika Anda memiliki kekhawatiran terkait data atau kolaborasi, silakan kirimkan pesan kepada kami.</p>
                
                <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2 px-3 border outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2 px-3 border outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                        <input type="text" name="subject" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2 px-3 border outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Utama</label>
                        <textarea name="message" rows="4" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2 px-3 border outline-none"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">Kirim Pesan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
