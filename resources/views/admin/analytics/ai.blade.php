@extends('admin.layouts.admin_layout')

@section('title', 'AI Usage Analytics - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Analytics</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">AI Usage</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">AI Usage Analytics</h2>
        <p class="text-slate-500 text-sm mt-1">Gemini API utilization, performance, and cost insights.</p>
    </div>
    <a href="{{ route('admin.analytics.export', 'ai') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-lg flex items-center space-x-1">
        <span class="material-icons-round text-sm">download</span>
        <span>Export</span>
    </a>
</div>

<!-- KPI -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800">
        <p class="text-xs text-slate-500 uppercase font-semibold">Total Requests</p>
        <p class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalRequests) }}</p>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800">
        <p class="text-xs text-slate-500 uppercase font-semibold">Error Rate</p>
        <p class="text-3xl font-extrabold {{ $errorRate > 5 ? 'text-red-500' : 'text-emerald-600' }} mt-1">{{ number_format($errorRate, 1) }}%</p>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800">
        <p class="text-xs text-slate-500 uppercase font-semibold">Avg Response Time</p>
        <p class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($avgResponseTime) }}<span class="text-sm text-slate-400">ms</span></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Daily Usage Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <h4 class="text-lg font-bold mb-4">Daily AI Requests (30d)</h4>
        <div class="h-64"><canvas id="aiUsageChart"></canvas></div>
    </div>

    <!-- Feature Usage -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <h4 class="text-lg font-bold mb-4">Feature Usage Breakdown</h4>
        <div class="space-y-3">
            @foreach($featureUsage as $feature)
            @php $pct = $totalRequests > 0 ? ($feature->count / $totalRequests) * 100 : 0; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">{{ $feature->feature }}</span>
                    <span class="font-bold">{{ number_format($feature->count) }}</span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const aiData = @json($dailyUsage);
new Chart(document.getElementById('aiUsageChart'), {
    type: 'line',
    data: {
        labels: aiData.map(d => d.date),
        datasets: [{
            label: 'AI Requests',
            data: aiData.map(d => d.count),
            borderColor: '#8B5CF6',
            backgroundColor: 'rgba(139,92,246,0.1)',
            borderWidth: 2, tension: 0.4, fill: true, pointRadius: 2
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true }, x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true } } }
    }
});
</script>
@endpush
