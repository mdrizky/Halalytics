@extends('admin.layouts.admin_layout')

@section('title', 'Analytics Dashboard - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Analytics Dashboard</span>
@endsection

@section('content')
<div class="flex items-start justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Analytics Dashboard</h2>
        <p class="text-slate-500 text-sm mt-1">Semua angka di halaman ini diambil dari data real database, bukan placeholder.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.analytics.export', 'users') }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            Export Users
        </a>
        <a href="{{ route('admin.analytics.export', 'scans') }}" class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark transition">
            Export Scans
        </a>
    </div>
</div>

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Total Users', number_format($data['overview']['total_users']), 'group', 'text-primary'],
        ['Users Baru Hari Ini', number_format($data['overview']['new_users_today']), 'person_add', 'text-emerald-500'],
        ['Total Scan', number_format($data['overview']['total_scans']), 'qr_code_scanner', 'text-amber-500'],
        ['Campaign Sent', number_format($data['overview']['campaigns_sent']), 'campaign', 'text-rose-500'],
    ] as [$label, $value, $icon, $color])
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider font-bold text-slate-400">{{ $label }}</p>
                    <p class="mt-3 text-3xl font-extrabold text-slate-800 dark:text-white">{{ $value }}</p>
                </div>
                <span class="material-icons-round {{ $color }}">{{ $icon }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Pertumbuhan User 30 Hari</h3>
                <p class="text-sm text-slate-500 mt-1">Growth rate minggu ini: {{ number_format($data['overview']['growth_rate'], 1) }}%</p>
            </div>
        </div>
        <div class="mt-6 h-80">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Distribusi Status Halal</h3>
        <div class="mt-6 h-80">
            <canvas id="halalStatusChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Aktivitas Scan 7 Hari</h3>
        <div class="mt-6 h-80">
            <canvas id="scanActivityChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Tren Health Tracking 30 Hari</h3>
        <div class="mt-6 h-80">
            <canvas id="healthTrendChart"></canvas>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Top Produk Paling Sering Di-scan</h3>
            <p class="text-sm text-slate-500 mt-1">Diurutkan dari scan terbanyak.</p>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] uppercase tracking-wider text-slate-400">
                    <th class="pb-3">Produk</th>
                    <th class="pb-3">Barcode</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Jumlah Scan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($data['top_products'] as $product)
                    <tr>
                        <td class="py-4 text-sm font-bold text-slate-800 dark:text-white">{{ $product['product_name'] }}</td>
                        <td class="py-4 text-sm text-slate-500">{{ $product['barcode'] ?: '—' }}</td>
                        <td class="py-4 text-sm">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                @if($product['halal_status'] === 'halal') bg-emerald-100 text-emerald-600
                                @elseif($product['halal_status'] === 'haram') bg-red-100 text-red-600
                                @else bg-amber-100 text-amber-600 @endif">
                                {{ ucfirst($product['halal_status']) }}
                            </span>
                        </td>
                        <td class="py-4 text-sm text-right font-bold text-slate-800 dark:text-white">{{ number_format($product['scan_count']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-slate-400">Belum ada data scan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const userGrowth = @json($data['user_growth']);
    const scanActivity = @json($data['scan_activity']);
    const halalStats = @json($data['halal_stats']);
    const healthTrends = @json($data['health_trends']);

    new Chart(document.getElementById('userGrowthChart'), {
        type: 'line',
        data: {
            labels: userGrowth.map(item => item.date),
            datasets: [{
                label: 'User baru',
                data: userGrowth.map(item => item.count),
                borderColor: '#00bbc2',
                backgroundColor: 'rgba(0, 187, 194, 0.12)',
                fill: true,
                tension: 0.35
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('halalStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Halal', 'Haram', 'Syubhat'],
            datasets: [{
                data: [halalStats.halal, halalStats.haram, halalStats.syubhat],
                backgroundColor: ['#059669', '#dc2626', '#d97706']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('scanActivityChart'), {
        type: 'bar',
        data: {
            labels: scanActivity.map(item => item.date),
            datasets: [
                { label: 'Halal', data: scanActivity.map(item => item.halal), backgroundColor: '#059669' },
                { label: 'Haram', data: scanActivity.map(item => item.haram), backgroundColor: '#dc2626' },
                { label: 'Syubhat', data: scanActivity.map(item => item.syubhat), backgroundColor: '#d97706' }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('healthTrendChart'), {
        type: 'bar',
        data: {
            labels: healthTrends.map(item => item.metric_type),
            datasets: [{
                label: 'Jumlah entry',
                data: healthTrends.map(item => item.count),
                backgroundColor: '#6366f1'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
