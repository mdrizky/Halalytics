@extends('expert.layouts.expert_layout')

@section('content')
<div class="space-y-8">
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="surface-card p-6 rounded-3xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Pasien</p>
            <h3 class="text-3xl font-black text-primary mt-2">{{ number_format($stats['total_patients']) }}</h3>
            <p class="text-[10px] text-emerald-500 font-bold mt-2 flex items-center gap-1">
                <span class="material-icons-round text-xs">trending_up</span> +2 Pasien baru
            </p>
        </div>
        <div class="surface-card p-6 rounded-3xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Konsultasi Aktif</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ number_format($stats['active_consultations']) }}</h3>
            <p class="text-[10px] text-slate-400 font-bold mt-2">Menunggu respon</p>
        </div>
        <div class="surface-card p-6 rounded-3xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rating Rata-rata</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ number_format($stats['avg_rating'], 1) }}</h3>
            <div class="flex text-amber-400 mt-2">
                @for($i=0; $i<5; $i++) <span class="material-icons-round text-xs">star</span> @endfor
            </div>
        </div>
        <div class="surface-card p-6 rounded-3xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selesai</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ number_format($stats['completed_consultations']) }}</h3>
            <p class="text-[10px] text-slate-400 font-bold mt-2">Total sesi bulan ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Feed -->
        <div class="lg:col-span-2 space-y-6">
            <div class="surface-card rounded-[2rem] p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Antrean Konsultasi</h3>
                    <button class="text-xs font-bold text-primary hover:underline">Lihat Semua</button>
                </div>

                <div class="space-y-4">
                    <div class="p-6 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                        <span class="material-icons-round text-3xl text-slate-200">chat_bubble_outline</span>
                        <p class="text-slate-400 text-sm mt-2">Belum ada permintaan konsultasi baru.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Activity -->
        <div class="space-y-6">
            <div class="surface-card rounded-[2rem] p-8">
                <h3 class="text-sm font-bold text-slate-800 mb-6">Aktivitas Pasien</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Daffa Rizky memperbarui BMI</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">2 menit yang lalu</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">User menanyakan kandungan produk</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">15 menit yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="metric-card">
                <h3 class="text-lg font-bold">AI Recommendation</h3>
                <p class="text-xs text-white/80 mt-2 leading-relaxed">Gunakan fitur AI Assist untuk membantu memberikan rekomendasi gizi yang akurat berdasarkan data scan pasien.</p>
                <button class="mt-4 px-6 py-2 bg-white text-primary text-xs font-bold rounded-xl shadow-lg shadow-black/10">Buka AI Assist</button>
            </div>
        </div>
    </div>
</div>
@endsection
