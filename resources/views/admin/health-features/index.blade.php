@extends('admin.layouts.admin_layout')
@section('title', 'AI Health Suite — Halalytics Admin')
@section('breadcrumb-parent', 'Admin')
@section('breadcrumb-current', 'AI Health Suite')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">AI Health Suite</h1>
        <p class="text-sm text-slate-500 mt-1">Monitor dan kelola fitur-fitur kesehatan AI di aplikasi Android.</p>
    </div>
    <div class="flex items-center space-x-3">
        <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
            <span class="material-icons-round text-sm align-middle mr-1">check_circle</span>
            {{ $activeCount ?? 0 }} Fitur Aktif
        </span>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="metric-card metric-card--primary">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/60 text-xs font-medium">Total Scan Makanan</p>
                <p class="text-2xl font-extrabold mt-1">{{ number_format($foodScanCount ?? 0) }}</p>
            </div>
            <span class="material-icons-round text-3xl text-white/30">camera_alt</span>
        </div>
        <p class="text-xs text-white/50 mt-2">Bulan ini</p>
    </div>
    <div class="metric-card metric-card--accent">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/60 text-xs font-medium">Voice Logs</p>
                <p class="text-2xl font-extrabold mt-1">{{ number_format($voiceLogCount ?? 0) }}</p>
            </div>
            <span class="material-icons-round text-3xl text-white/30">mic</span>
        </div>
        <p class="text-xs text-white/50 mt-2">Pencatatan suara</p>
    </div>
    <div class="metric-card metric-card--soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/60 text-xs font-medium">AI Consultations</p>
                <p class="text-2xl font-extrabold mt-1">{{ number_format($aiConsultCount ?? 0) }}</p>
            </div>
            <span class="material-icons-round text-3xl text-white/30">smart_toy</span>
        </div>
        <p class="text-xs text-white/50 mt-2">Konsultasi AI</p>
    </div>
    <div class="metric-card metric-card--danger">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/60 text-xs font-medium">Active Users</p>
                <p class="text-2xl font-extrabold mt-1">{{ number_format($healthActiveUsers ?? 0) }}</p>
            </div>
            <span class="material-icons-round text-3xl text-white/30">groups</span>
        </div>
        <p class="text-xs text-white/50 mt-2">Pengguna fitur kesehatan</p>
    </div>
</div>

{{-- Feature Toggle Grid --}}
<div class="surface-card rounded-2xl p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">
            <span class="material-icons-round text-primary align-middle mr-2">toggle_on</span>
            Fitur & Status
        </h2>
        <span class="text-xs text-slate-400">Toggle untuk mengaktifkan/menonaktifkan fitur di aplikasi</span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($features ?? [] as $feature)
        <div class="border border-slate-100 dark:border-slate-800 rounded-xl p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: {{ $feature['color'] ?? '#004D40' }}">
                        <span class="material-icons-round text-xl">{{ $feature['icon'] ?? 'extension' }}</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-800 dark:text-white">{{ $feature['name'] }}</h3>
                        <p class="text-xs text-slate-400">{{ $feature['description'] }}</p>
                    </div>
                </div>
                <label class="toggle-switch flex-shrink-0 mt-1">
                    <input type="checkbox" {{ ($feature['enabled'] ?? true) ? 'checked' : '' }}
                           onchange="toggleFeature('{{ $feature['key'] }}', this.checked)">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex items-center mt-3 space-x-4">
                <span class="text-xs text-slate-400">
                    <span class="material-icons-round text-[14px] align-middle mr-0.5">people</span>
                    {{ number_format($feature['usage_count'] ?? 0) }} pengguna
                </span>
                @if($feature['status'] === 'active')
                    <span class="badge-active text-[10px] px-2 py-0.5 rounded-full font-bold">Active</span>
                @elseif($feature['status'] === 'beta')
                    <span class="badge-syubhat text-[10px] px-2 py-0.5 rounded-full font-bold">Beta</span>
                @else
                    <span class="badge-pending text-[10px] px-2 py-0.5 rounded-full font-bold">Coming Soon</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- User Health Activity Table --}}
<div class="surface-card rounded-2xl p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">
            <span class="material-icons-round text-primary align-middle mr-2">analytics</span>
            Aktivitas Kesehatan Pengguna (Terbaru)
        </h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-100 dark:border-slate-800">
                    <th class="pb-3 font-semibold text-slate-500">Pengguna</th>
                    <th class="pb-3 font-semibold text-slate-500">Fitur</th>
                    <th class="pb-3 font-semibold text-slate-500">Detail</th>
                    <th class="pb-3 font-semibold text-slate-500">Waktu</th>
                    <th class="pb-3 font-semibold text-slate-500">Konsistensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivity ?? [] as $activity)
                <tr class="border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="py-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($activity['user_name'] ?? 'U', 0, 1)) }}
                            </div>
                            <span class="font-medium">{{ $activity['user_name'] ?? 'User' }}</span>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-lg text-xs font-bold bg-primary/10 text-primary">
                            {{ $activity['feature'] ?? '-' }}
                        </span>
                    </td>
                    <td class="py-3 text-slate-500">{{ Str::limit($activity['detail'] ?? '-', 40) }}</td>
                    <td class="py-3 text-slate-400">{{ $activity['time'] ?? '-' }}</td>
                    <td class="py-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $activity['consistency'] ?? 0 }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-500">{{ $activity['consistency'] ?? 0 }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2 block">health_and_safety</span>
                        Belum ada aktivitas kesehatan tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Feature Roadmap --}}
<div class="surface-card rounded-2xl p-6">
    <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4">
        <span class="material-icons-round text-primary align-middle mr-2">rocket_launch</span>
        Roadmap Fitur
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="border-2 border-emerald-200 dark:border-emerald-800 rounded-xl p-4 bg-emerald-50/50 dark:bg-emerald-900/10">
            <h3 class="font-bold text-emerald-700 dark:text-emerald-400 mb-3 text-sm">✅ Dirilis</h3>
            <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Calorie Counter</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Water Tracker</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>AI Health Assistant</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Food Photo Scanner</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Voice Food Logging</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Nutrition Label OCR</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Recipe Engine</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>BMI Calculator</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-emerald-500 text-sm">check</span><span>Daily Missions (Gamification)</span></li>
            </ul>
        </div>
        <div class="border-2 border-amber-200 dark:border-amber-800 rounded-xl p-4 bg-amber-50/50 dark:bg-amber-900/10">
            <h3 class="font-bold text-amber-700 dark:text-amber-400 mb-3 text-sm">🔧 Beta / Dalam Pengembangan</h3>
            <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                <li class="flex items-center space-x-2"><span class="material-icons-round text-amber-500 text-sm">pending</span><span>Sleep Tracker</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-amber-500 text-sm">pending</span><span>Smart Contextual Reminders</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-amber-500 text-sm">pending</span><span>Menu Restoran AI</span></li>
            </ul>
        </div>
        <div class="border-2 border-slate-200 dark:border-slate-700 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-800/30">
            <h3 class="font-bold text-slate-500 mb-3 text-sm">📋 Direncanakan</h3>
            <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                <li class="flex items-center space-x-2"><span class="material-icons-round text-slate-400 text-sm">schedule</span><span>Wearable Integration (Google Fit)</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-slate-400 text-sm">schedule</span><span>Auto Grocery List</span></li>
                <li class="flex items-center space-x-2"><span class="material-icons-round text-slate-400 text-sm">schedule</span><span>Precision Nutrition (DNA)</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function toggleFeature(key, enabled) {
    try {
        const response = await fetch('{{ route("admin.health-features.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ key, enabled })
        });
        const data = await response.json();
        if (!data.success) {
            alert('Gagal mengubah status fitur.');
        }
    } catch (error) {
        console.error('Toggle error:', error);
        alert('Terjadi kesalahan saat mengubah status fitur.');
    }
}
</script>
@endpush
