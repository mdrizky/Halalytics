@extends('promo.layout')

@section('title', 'Dashboard User - ' . ($settings['site_name'] ?? 'Halalytics'))

@section('content')
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Halo, {{ $user->name }}! 👋</h1>
                <p class="text-gray-600 mt-1">Selamat datang kembali di dashboard kesehatan halal Anda.</p>
            </div>
            <div class="flex items-center gap-3 bg-white p-3 rounded-2xl shadow-sm border border-emerald-100">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold text-xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                    <div class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full inline-block font-bold">USER</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Stats & Scans -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm card-hover">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                <span class="material-icons-round">qr_code_scanner</span>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $recentScans->count() }}</div>
                                <div class="text-sm text-gray-500">Total Scan Bulan Ini</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm card-hover">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                                <span class="material-icons-round">verified</span>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">100%</div>
                                <div class="text-sm text-gray-500">Akurasi Halal</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Scans Table -->
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-lg">Riwayat Scan Terakhir</h3>
                        <a href="#" class="text-sm text-emerald-600 font-bold hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Produk</th>
                                    <th class="px-6 py-4 font-semibold">Status</th>
                                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentScans as $scan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $scan->nama_produk ?? 'Produk Tanpa Nama' }}</div>
                                        <div class="text-xs text-gray-500">{{ $scan->barcode }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $scan->status_halal == 'halal' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ strtoupper($scan->status_halal) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('d M Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <span class="material-icons-round text-4xl mb-2 text-gray-300">history</span>
                                            <p>Belum ada riwayat scan.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recommendations & Tools -->
            <div class="space-y-8">
                <!-- Recommended Articles -->
                <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <h3 class="font-bold text-gray-900 text-lg mb-4">Rekomendasi Artikel</h3>
                    <div class="space-y-4">
                        @foreach($recommendedArticles as $article)
                        <a href="{{ route('blog.show', $article->slug) }}" class="group block">
                            <div class="flex gap-4">
                                <img src="{{ $article->image_url }}" alt="" class="w-20 h-20 rounded-2xl object-cover bg-gray-100 flex-shrink-0" onerror="handleImgError(this, 'https://loremflickr.com/200/200/health?lock={{ $article->id }}')">
                                <div class="flex-grow">
                                    <h4 class="text-sm font-bold text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2">{{ $article->title }}</h4>
                                    <span class="text-[10px] text-gray-500 uppercase font-bold">{{ $article->category }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- CTA App -->
                <div class="bg-emerald-900 p-8 rounded-[2.5rem] text-white relative overflow-hidden shadow-xl">
                    <div class="relative z-10">
                        <h3 class="font-bold text-xl mb-2">Fitur Lengkap di App</h3>
                        <p class="text-emerald-100 text-sm mb-6 leading-relaxed">Gunakan aplikasi mobile untuk akses scan kamera AI dan monitor kesehatan 24/7.</p>
                        <a href="{{ route('download') }}" class="bg-white text-emerald-900 px-6 py-3 rounded-full font-bold text-sm inline-block shadow-lg">Download Sekarang</a>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
