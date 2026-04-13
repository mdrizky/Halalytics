@extends('admin.layouts.admin_layout')

@section('title', 'Dashboard - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Overview</span>
@endsection

@section('content')
<!-- Page Title & Date Filter -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Analytics Dashboard</h2>
        <p class="text-slate-500 text-sm mt-1">Real-time performance metrics for Halalytics platform.</p>
    </div>
    <div class="flex items-center space-x-2 bg-white dark:bg-slate-900 p-1.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <button class="px-4 py-1.5 text-xs font-bold rounded-lg bg-primary text-white" onclick="filterPeriod('30days')">30 Days</button>
        <button class="px-4 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800" onclick="filterPeriod('90days')">90 Days</button>
        <button class="px-4 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800" onclick="filterPeriod('year')">1 Year</button>
        <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
        <button class="px-2 py-1.5 text-slate-500 hover:text-primary">
            <span class="material-icons-round text-lg">calendar_today</span>
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Categories -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-halal">
                <span class="material-icons-round">category</span>
            </div>
            <span class="text-[10px] font-extrabold px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-halal rounded-full">Active</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Categories</p>
        <h3 id="totalKategori-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalKategori) }}</h3>
    </div>
    
    <!-- Card 2: Total Products -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-icons-round">inventory_2</span>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-extrabold px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-halal rounded-full mb-1">Combined DB</span>
                <span class="text-[9px] text-slate-400 font-bold uppercase">{{ number_format($offProduk) }} from OFF</span>
            </div>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Products</p>
        <div class="flex items-baseline space-x-2 mt-1">
            <h3 id="totalProduk-value" class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($totalProduk) }}</h3>
            <span class="text-xs text-slate-400 font-medium">({{ number_format($localProduk) }} local)</span>
        </div>
    </div>
    
    <!-- Card 3: Total Users -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-icons-round">people</span>
            </div>
            <span class="text-[10px] font-extrabold px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-halal rounded-full">+8.1%</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Users</p>
        <h3 id="totalUsers-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalUsers) }}</h3>
    </div>
    
    <!-- Card 4: Total Scans -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600">
                <span class="material-icons-round">qr_code_2</span>
            </div>
            <span class="text-[10px] font-extrabold px-2 py-1 bg-primary/10 text-primary rounded-full">{{ number_format($scanToday) }} today</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Scans</p>
        <h3 id="totalScan-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($totalScan) }}</h3>
    </div>
</div>

<!-- Main Grid Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Scan Activity Chart -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">Scan Activity</h4>
                    <p class="text-sm text-slate-400">Aggregated daily product scans</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                        <span class="text-xs font-medium text-slate-500">Current Period</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                        <span class="text-xs font-medium text-slate-500">Prev Period</span>
                    </div>
                </div>
            </div>
            <!-- Chart Container -->
            <div class="relative h-64 w-full">
                <canvas id="scanActivityChart"></canvas>
            </div>
        </div>
        
        <!-- Recent Scan Log (Mini Table) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Live Scan Feed</h4>
                <a href="{{ route('admin.scan.index') }}" class="text-sm font-bold text-primary hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentScans as $scan)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800 mr-3 flex items-center justify-center">
                                        <span class="material-icons-round text-sm text-slate-400">fastfood</span>
                                    </div>
                                    <span class="text-sm font-medium">{{ $scan->nama_produk ?? 'Unknown Product' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $scan->username ?? 'Anonymous' }}</td>
                            <td class="px-6 py-4">
                                @if($scan->status_halal == 'halal')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600">Halal</span>
                                @elseif($scan->status_halal == 'syubhat')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-600">Syubhat</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/30 text-red-600">Haram</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                <span class="material-icons-round text-3xl mb-2">inbox</span>
                                <p>No recent scans</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Top Scanned Products -->
    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Top Scanned</h4>
                <span class="material-icons-round text-slate-300">more_vert</span>
            </div>
            <div class="space-y-5">
                @forelse($topScannedProducts as $index => $product)
                <!-- Item {{ $index + 1 }} -->
                <div class="flex items-center group cursor-pointer">
                    <div class="relative w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center transition-colors group-hover:bg-primary/10">
                        <span class="material-icons-round text-slate-400">inventory_2</span>
                        <div class="absolute -top-2 -left-2 w-6 h-6 {{ $index === 0 ? 'bg-primary text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }} text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">#{{ $index + 1 }}</div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $product->nama_produk }}</p>
                        <div class="flex items-center text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">
                            @if($product->status_halal == 'halal')
                            <span class="text-emerald-halal">Halal</span>
                            @elseif($product->status_halal == 'syubhat')
                            <span class="text-amber-syubhat">Syubhat</span>
                            @else
                            <span class="text-red-haram">Haram</span>
                            @endif
                            <span class="mx-2">•</span>
                            <span>{{ $product->barcode }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-extrabold text-slate-800 dark:text-white">{{ number_format($product->scan_count) }}</p>
                        <p class="text-[10px] text-slate-400">scans</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-slate-400">
                    <span class="material-icons-round text-3xl mb-2">inventory_2</span>
                    <p>No products scanned yet</p>
                </div>
                @endforelse
            </div>
            <a href="{{ route('admin.product.index') }}" class="w-full mt-8 py-3 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold rounded-xl border border-dashed border-slate-300 dark:border-slate-700 hover:border-primary hover:text-primary transition-all flex items-center justify-center">
                VIEW ALL PRODUCTS
            </a>
        </div>
        
        <!-- Platform Status Card -->
        <div class="bg-primary p-6 rounded-xl shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-white font-bold mb-2">Platform Status</h4>
                <div class="flex items-center space-x-2 text-primary-foreground/80 mb-4">
                    <div class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></div>
                    <p class="text-white/80 text-xs font-medium">All systems operational</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm">
                    <div class="flex justify-between items-end">
                        <p class="text-[10px] text-white/70 uppercase font-bold">Scans This Week</p>
                        <p class="text-white font-bold">{{ number_format($scanThisWeek) }}</p>
                    </div>
                    <div class="w-full h-1 bg-white/20 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-white rounded-full" style="width: {{ min(($scanThisWeek / max($totalScan, 1)) * 100 * 10, 100) }}%"></div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="bg-white/10 rounded-lg p-2">
                        <p class="text-[10px] text-white/70">Pending Reports</p>
                        <p class="text-white font-bold">{{ number_format($laporanMasuk) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-2">
                        <p class="text-[10px] text-white/70">Halal Products</p>
                        <p class="text-white font-bold">{{ number_format($produkHalal) }}</p>
                    </div>
                </div>
            </div>
            <!-- Abstract Graphic -->
            <div class="absolute -right-10 -bottom-10 opacity-20 transform rotate-12">
                <span class="material-icons-round text-9xl text-white">fingerprint</span>
            </div>
        </div>
        
        <!-- Product Status Summary -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Product Status</h4>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Halal</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ number_format($produkHalal) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Syubhat</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ number_format($produkDiragukan) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Haram</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ number_format($produkHaram) }}</span>
                </div>
            </div>
            <div class="mt-4 h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden flex">
                @php
                    $total = $produkHalal + $produkDiragukan + $produkHaram;
                    $halalPercent = $total > 0 ? ($produkHalal / $total) * 100 : 0;
                    $syubhatPercent = $total > 0 ? ($produkDiragukan / $total) * 100 : 0;
                    $haramPercent = $total > 0 ? ($produkHaram / $total) * 100 : 0;
                @endphp
                <div class="h-full bg-emerald-500" style="width: {{ $halalPercent }}%"></div>
                <div class="h-full bg-amber-500" style="width: {{ $syubhatPercent }}%"></div>
                <div class="h-full bg-red-500" style="width: {{ $haramPercent }}%"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart instance
    let scanChart;
    
    // Chart data from controller
    const labels30 = @json($labels30Hari);
    const data30 = @json($data30Hari);
    
    // Init Chart
    function initChart(labels, data) {
        const ctx = document.getElementById('scanActivityChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 256);
        gradient.addColorStop(0, 'rgba(0, 187, 194, 0.15)');
        gradient.addColorStop(1, 'rgba(0, 187, 194, 0)');
        
        if (scanChart) scanChart.destroy();
        
        scanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Scans',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#00bbc2',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#00bbc2',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        borderColor: 'rgba(0, 187, 194, 0.3)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 0 }
                    }
                }
            }
        });
    }

    // Call init on load
    initChart(labels30, data30);
    
    // Refresh stats function
    async function refreshStats(period = 30) {
        try {
            const response = await fetch(`{{ route('admin.dashboard.stats') }}?period=${period}`);
            const result = await response.json();
            
            if (result.success) {
                // Update KPI Cards
                updateKPI('totalKategori', result.stats.totalKategori); // If we have it in result
                updateKPI('totalProduk', result.stats.totalProduk);
                updateKPI('totalUsers', result.stats.totalUsers);
                updateKPI('totalScan', result.stats.totalScan);
                
                // Update Chart
                initChart(result.chart.labels, result.chart.data);
            }
        } catch (err) {
            console.error("Failed to refresh stats:", err);
        }
    }

    function updateKPI(id, value) {
        const el = document.getElementById(id + '-value');
        if (el) el.innerText = new Intl.NumberFormat().format(value);
    }
    
    // Period filter function
    function filterPeriod(days) {
        // Update button states
        document.querySelectorAll('[onclick^="filterPeriod"]').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('text-slate-500');
        });
        
        event.target.classList.add('bg-primary', 'text-white');
        event.target.classList.remove('text-slate-500');
        
        refreshStats(days);
    }

    // Auto-refresh every 10 seconds
    setInterval(() => {
        const activePeriodBtn = document.querySelector('.bg-primary.text-white[onclick^="filterPeriod"]');
        const periodText = activePeriodBtn ? activePeriodBtn.innerText : '30 Days';
        let days = 30;
        if (periodText === '90 Days') days = 90;
        if (periodText === '1 Year') days = 365;
        
        refreshStats(days);
    }, 10000);
</script>
@endpush
