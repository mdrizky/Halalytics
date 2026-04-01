@extends('admin.layouts.admin_layout')

@section('title', 'Growth Analytics - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Analytics</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Growth</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Growth Analytics</h2>
        <p class="text-slate-500 text-sm mt-1">DAU trends, scan volume, and community contributions.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- DAU Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <h4 class="text-lg font-bold mb-4">Daily Active Users (30d)</h4>
        <div class="h-64"><canvas id="dauChart"></canvas></div>
    </div>

    <!-- Scans Per Day -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <h4 class="text-lg font-bold mb-4">Daily Scans (30d)</h4>
        <div class="h-64"><canvas id="scansChart"></canvas></div>
    </div>

    <!-- Weekly Contributions -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <h4 class="text-lg font-bold mb-4">Weekly Community Contributions (3 months)</h4>
        <div class="h-64"><canvas id="contribChart"></canvas></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dauData = @json($dau);
const scansData = @json($scansPerDay);
const contribData = @json($contributionsPerWeek);

new Chart(document.getElementById('dauChart'), {
    type: 'line',
    data: { labels: dauData.map(d => d.date), datasets: [{ label: 'DAU', data: dauData.map(d => d.users), borderColor: '#00bbc2', backgroundColor: 'rgba(0,187,194,0.1)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 2 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true } } } }
});

new Chart(document.getElementById('scansChart'), {
    type: 'bar',
    data: { labels: scansData.map(d => d.date), datasets: [{ label: 'Scans', data: scansData.map(d => d.scans), backgroundColor: 'rgba(59,130,246,0.6)', borderRadius: 4 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true } } } }
});

new Chart(document.getElementById('contribChart'), {
    type: 'bar',
    data: { labels: contribData.map(d => 'W' + d.week), datasets: [{ label: 'Contributions', data: contribData.map(d => d.count), backgroundColor: 'rgba(16,185,129,0.6)', borderRadius: 4 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
});
</script>
@endpush
