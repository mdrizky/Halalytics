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
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Verifikasi Produk</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ number_format($stats['pending_verifications'] ?? 0) }}</h3>
            <p class="text-[10px] text-amber-500 font-bold mt-2">Menunggu tinjauan gizi</p>
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
                    <h3 class="text-lg font-bold text-slate-800">Aktivitas Kesehatan Terbaru</h3>
                    <a href="{{ route('admin.health-features.index') }}" class="text-xs font-bold text-primary hover:underline">Monitor Fitur</a>
                </div>

                <div class="space-y-4">
                    @forelse($patientActivities ?? [] as $activity)
                    <div class="p-4 flex items-center justify-between border border-slate-100 rounded-2xl hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 font-black">
                                {{ substr($activity->user_full_name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $activity->user_full_name ?? 'Guest' }}</p>
                                <p class="text-xs text-slate-500">{{ $activity->summary }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold uppercase">{{ str_replace('_', ' ', $activity->event_type) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                        <span class="material-icons-round text-3xl text-slate-200">monitor_heart</span>
                        <p class="text-slate-400 text-sm mt-2">Belum ada aktivitas kesehatan pasien.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Activity -->
        <div class="space-y-6">
            <div class="surface-card rounded-[2rem] p-8">
                <h3 class="text-sm font-bold text-slate-800 mb-6">Status Sistem Gizi</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Database Obat FDA Terkoneksi</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Real-time sync aktif</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">AI Analysis Engine v2.5</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Optimal</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setInterval(function() {
        fetch(window.location.href, {
            headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const oldStats = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4');
            const newStats = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-4');
            if (oldStats && newStats) {
                oldStats.innerHTML = newStats.innerHTML;
            }
        })
        .catch(() => {});
    }, 30000);
});
</script>
@endpush
@endsection
