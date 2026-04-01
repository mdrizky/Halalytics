@extends('admin.layouts.admin_layout')

@section('title', 'User Analytics - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Analytics</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Users</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">User Analytics</h2>
        <p class="text-slate-500 text-sm mt-1">Registration trends, active users, and engagement metrics.</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.analytics.products') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">Products</a>
        <a href="{{ route('admin.analytics.ai') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">AI Usage</a>
        <a href="{{ route('admin.analytics.growth') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">Growth</a>
        <a href="{{ route('admin.analytics.export', 'users') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-lg flex items-center space-x-1">
            <span class="material-icons-round text-sm">download</span>
            <span>Export CSV</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                <span class="material-icons-round">people</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase">Total Users</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($totalUsers) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                <span class="material-icons-round">today</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase">Active Today</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($activeToday) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-violet-600">
                <span class="material-icons-round">date_range</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase">Active This Week</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($activeWeek) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                <span class="material-icons-round">calendar_month</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase">Active This Month</p>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($activeMonth) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Registration Chart -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-8">
    <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">New Registrations (30 Days)</h4>
    <div class="relative h-72">
        <canvas id="registrationChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
const regData = @json($registrations);
new Chart(document.getElementById('registrationChart'), {
    type: 'bar',
    data: {
        labels: regData.map(r => r.date),
        datasets: [{
            label: 'New Users',
            data: regData.map(r => r.count),
            backgroundColor: 'rgba(0, 187, 194, 0.6)',
            borderColor: '#00bbc2',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.1)' } },
            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 15 } }
        }
    }
});
</script>
@endpush
