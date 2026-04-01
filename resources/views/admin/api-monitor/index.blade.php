@extends('admin.layouts.admin_layout')

@section('title', 'API Health Monitor - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">API Health Monitor</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">API Health Monitor</h2>
        <p class="text-slate-500 text-sm mt-1">Real-time status of all external API integrations.</p>
    </div>
    <form action="{{ route('admin.api-monitor.check') }}" method="POST">
        @csrf
        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition flex items-center space-x-2">
            <span class="material-icons-round text-sm">refresh</span>
            <span>Check Now</span>
        </button>
    </form>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<!-- API Status Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach($apis as $api)
    @php $data = $current[$api]; @endphp
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">{{ $api }}</h3>
            @if($data['status'] === 'up')
                <span class="flex items-center space-x-1 px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span>UP</span>
                </span>
            @elseif($data['status'] === 'slow')
                <span class="flex items-center space-x-1 px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                    <span>SLOW</span>
                </span>
            @elseif($data['status'] === 'degraded')
                <span class="flex items-center space-x-1 px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                    <span>DEGRADED</span>
                </span>
            @else
                <span class="flex items-center space-x-1 px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span>DOWN</span>
                </span>
            @endif
        </div>

        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Latency</span>
                <span class="font-bold {{ $data['avg_latency'] > 1000 ? 'text-amber-600' : 'text-slate-800 dark:text-white' }}">
                    {{ $data['latency_ms'] ? number_format($data['latency_ms']) . 'ms' : 'N/A' }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Uptime 24h</span>
                <span class="font-bold {{ $data['uptime_24h'] >= 99 ? 'text-emerald-600' : ($data['uptime_24h'] >= 95 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $data['uptime_24h'] }}%
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Uptime 7d</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $data['uptime_7d'] }}%</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Last Check</span>
                <span class="text-xs text-slate-400">{{ $data['last_check'] ? \Carbon\Carbon::parse($data['last_check'])->diffForHumans() : 'Never' }}</span>
            </div>
        </div>

        <a href="{{ route('admin.api-monitor.history', $api) }}" class="mt-4 block text-center py-2 text-xs font-bold text-primary hover:underline">
            View History →
        </a>
    </div>
    @endforeach
</div>

<!-- Latency Chart -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Latency History (24h)</h4>
    <div class="relative h-64">
        <canvas id="latencyChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
const historyData = @json($history);
const colors = { gemini: '#8B5CF6', openfoodfacts: '#10B981', fda: '#3B82F6', openbeautyfacts: '#EC4899' };

const datasets = Object.entries(historyData).map(([api, logs]) => ({
    label: api.toUpperCase(),
    data: logs.map(l => ({ x: l.checked_at, y: l.latency_ms })),
    borderColor: colors[api] || '#6B7280',
    borderWidth: 2,
    pointRadius: 1,
    tension: 0.3,
    fill: false,
}));

new Chart(document.getElementById('latencyChart'), {
    type: 'line',
    data: { datasets },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { type: 'time', time: { unit: 'hour' }, grid: { display: false } },
            y: { beginAtZero: true, title: { display: true, text: 'ms' } }
        }
    }
});
</script>
@endpush
