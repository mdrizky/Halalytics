@extends('admin.layouts.admin_layout')

@section('title', 'Dashboard - Halalytics Admin')
@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-current', 'Overview')

@section('content')
<!-- Page Title & Date Filter -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Analytics Dashboard</h2>
        <p class="text-slate-500 text-sm mt-1">Real-time performance metrics for Halalytics platform.</p>
    </div>
    <div class="flex items-center space-x-2 bg-white dark:bg-slate-900 p-1.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <button data-period="30" class="period-btn px-4 py-1.5 text-xs font-bold rounded-lg {{ ($period_days ?? 30) === 30 ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">30 Days</button>
        <button data-period="90" class="period-btn px-4 py-1.5 text-xs font-bold rounded-lg {{ ($period_days ?? 30) === 90 ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">90 Days</button>
        <button data-period="365" class="period-btn px-4 py-1.5 text-xs font-bold rounded-lg {{ ($period_days ?? 30) === 365 ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">1 Year</button>
        <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
        <button class="px-2 py-1.5 text-slate-500 hover:text-primary">
            <span class="material-icons-round text-lg">calendar_today</span>
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Categories -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                <span class="material-icons-round">category</span>
            </div>
            <span class="text-[10px] font-extrabold px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-full">+4.2%</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Categories</p>
        <h3 id="totalKategori-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $stats['categories'] ?? 0 }}</h3>
    </div>
    
    <!-- Card 2: Products -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-icons-round">inventory_2</span>
            </div>
            <span class="text-[10px] font-extrabold px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-full">+12.5%</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Products</p>
        <h3 id="totalProduk-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($stats['products'] ?? 0) }}</h3>
    </div>
    
    <!-- Card 3: Users -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                <span class="material-icons-round">people</span>
            </div>
            <span class="text-[10px] font-extrabold px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-full">+8.1%</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Users</p>
        <h3 id="totalUsers-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ number_format($stats['users'] ?? 0) }}</h3>
    </div>
    
    <!-- Card 4: Scans -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600">
                <span class="material-icons-round">qr_code_2</span>
            </div>
            @php
                $scanChange = ($stats['scans_change'] ?? 0);
                $isPositive = $scanChange >= 0;
            @endphp
            <span class="text-[10px] font-extrabold px-2 py-1 {{ $isPositive ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-orange-50 dark:bg-orange-900/20 text-orange-600' }} rounded-full">{{ $isPositive ? '+' : '' }}{{ $scanChange }}%</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Scans</p>
        @php
            $scans = $stats['scans'] ?? 0;
            if ($scans >= 1000000) {
                $scansFormatted = number_format($scans / 1000000, 1) . 'M';
            } elseif ($scans >= 1000) {
                $scansFormatted = number_format($scans / 1000, 1) . 'k';
            } else {
                $scansFormatted = number_format($scans);
            }
        @endphp
        <h3 id="totalScan-value" class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $scansFormatted }}</h3>
    </div>
</div>

<!-- External Data Integration Status -->
<div class="mb-8 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">External Data Sources</h4>
        <span class="text-[11px] font-bold text-emerald-600">Realtime Sync Active</span>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-lg bg-slate-50 dark:bg-slate-800/50 p-3">
            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Local DB</p>
            <p class="text-xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['local_products'] ?? 0) }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 dark:bg-slate-800/50 p-3">
            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Open Food Facts</p>
            <p class="text-xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['open_food_facts_products'] ?? 0) }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 dark:bg-slate-800/50 p-3">
            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Open Beauty Facts</p>
            <p class="text-xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['open_beauty_facts_products'] ?? 0) }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 dark:bg-slate-800/50 p-3">
            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">OpenFDA Medicines</p>
            <p class="text-xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['openfda_medicines'] ?? 0) }}</p>
        </div>
    </div>
</div>

<!-- Realtime Monitor (User Compose -> Laravel Admin) -->
<div class="mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">External Scans</p>
            <p id="monitorExternalScans" class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_external_scans'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">Skincare Analyses</p>
            <p id="monitorSkincare" class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_skincare_analyses'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">Interaction Checks</p>
            <p id="monitorInteractions" class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_interaction_checks'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">Major/Contra</p>
            <p id="monitorMajorContra" class="text-2xl font-extrabold text-red-600">{{ number_format($monitor_stats['major_or_contra_count'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">Risk Checks</p>
            <p id="monitorRiskChecks" class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_risk_checks'] ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">Drug-Food Conflicts</p>
            <p id="monitorDrugFoodConflicts" class="text-2xl font-extrabold text-amber-600">{{ number_format($monitor_stats['total_drug_food_conflicts'] ?? 0) }}</p>
        </div>
    </div>
</div>

<!-- Main Grid Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Scan Activity Chart + Live Feed -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Scan Activity Chart -->
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
            
            <div class="relative h-64 w-full">
                <canvas id="scanActivityChart"></canvas>
            </div>
        </div>
        
        <!-- Live Scan Feed -->
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
                        @forelse($recent_scans ?? [] as $scan)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800 mr-3 flex items-center justify-center overflow-hidden">
                                        @if(!empty($scan->image))
                                        @php
                                            $scanImage = str_starts_with((string) $scan->image, 'http') ? $scan->image : asset($scan->image);
                                        @endphp
                                        <img src="{{ $scanImage }}" alt="{{ $scan->product_name ?? 'Product' }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="material-icons-round text-sm text-slate-400" style="display:none">fastfood</span>
                                        @else
                                        <span class="material-icons-round text-sm text-slate-400">fastfood</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium">{{ $scan->product_name ?? 'Unknown Product' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ optional($scan->user)->username ?? optional($scan->user)->full_name ?? 'Guest' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $status = strtolower($scan->status_halal ?? 'unknown');
                                    $statusClass = match($status) {
                                        'halal' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600',
                                        'haram' => 'bg-red-100 dark:bg-red-900/30 text-red-600',
                                        'syubhat', 'mushbooh' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600',
                                        default => 'bg-slate-100 dark:bg-slate-800 text-slate-500'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $scan->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                <span class="material-icons-round text-4xl mb-2">inbox</span>
                                <p>No recent scans</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Realtime Activity Feed -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Realtime Activity Feed</h4>
                <span class="text-xs font-bold text-emerald-600">Live + Polling Fallback</span>
            </div>
            <div id="realtime-feed-list" class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($activity_feed ?? [] as $event)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $event->summary ?? $event->event_type }}</p>
                        <span class="text-[10px] font-bold uppercase {{ ($event->status ?? '') === 'success' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $event->status ?? 'info' }}</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ $event->user_name ?? 'Guest' }} · {{ $event->event_type ?? '-' }} · {{ \Carbon\Carbon::parse($event->created_at)->diffForHumans() }}
                    </p>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada activity event.</div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Right Column: Top Scanned & Platform Status -->
    <div class="space-y-6">
        <!-- Top Scanned Products -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Top Scanned</h4>
                <span class="material-icons-round text-slate-300">more_vert</span>
            </div>
            <div class="space-y-5">
                @forelse($top_products ?? [] as $index => $product)
                <div class="flex items-center group cursor-pointer">
                    <div class="relative w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center transition-colors group-hover:bg-primary/10">
                        @if($product->image)
                        @php
                            $topImage = str_starts_with((string) $product->image, 'http') ? $product->image : asset($product->image);
                        @endphp
                        <img src="{{ $topImage }}" alt="{{ $product->product_name }}" class="w-8 h-8 object-cover rounded shadow-sm" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="material-icons-round text-slate-400" style="display:none">inventory_2</span>
                        @else
                        <span class="material-icons-round text-slate-400">inventory_2</span>
                        @endif
                        <div class="absolute -top-2 -left-2 w-6 h-6 {{ $index === 0 ? 'bg-primary text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }} text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">#{{ $index + 1 }}</div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ Str::limit($product->product_name, 20) }}</p>
                        <div class="flex items-center text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">
                            @php
                                $topStatus = strtolower((string) ($product->halal_status ?? 'pending'));
                                $topStatusClass = match ($topStatus) {
                                    'halal' => 'text-emerald-600',
                                    'haram', 'tidak halal' => 'text-red-600',
                                    'syubhat', 'diragukan' => 'text-amber-600',
                                    default => 'text-slate-500',
                                };
                            @endphp
                            <span class="{{ $topStatusClass }}">{{ $product->halal_status ?? 'Pending' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $product->category_name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $scanCount = $product->scan_count ?? 0;
                            if ($scanCount >= 1000) {
                                $scanFormatted = number_format($scanCount / 1000, 1) . 'k';
                            } else {
                                $scanFormatted = number_format($scanCount);
                            }
                        @endphp
                        <p class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $scanFormatted }}</p>
                        <p class="text-[10px] text-slate-400">scans</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400">
                    <span class="material-icons-round text-4xl mb-2">trending_up</span>
                    <p>No product data yet</p>
                </div>
                @endforelse
            </div>
            <a href="{{ route('admin.product.index') }}" class="block w-full mt-8 py-3 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold rounded-xl border border-dashed border-slate-300 dark:border-slate-700 hover:border-primary hover:text-primary transition-all text-center">
                VIEW ALL PRODUCTS
            </a>
        </div>
        
        <!-- Platform Status -->
        <div class="bg-primary p-6 rounded-xl shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-white font-bold mb-2">Platform Status</h4>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></div>
                    <p class="text-white/80 text-xs font-medium">All systems operational</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm">
                    <div class="flex justify-between items-end">
                        <p class="text-[10px] text-white/70 uppercase font-bold">API Latency</p>
                        <p class="text-white font-bold">{{ $stats['api_latency'] ?? '42' }}ms</p>
                    </div>
                    <div class="w-full h-1 bg-white/20 rounded-full mt-2 overflow-hidden">
                        <div class="w-3/4 h-full bg-white rounded-full"></div>
                    </div>
                </div>
            </div>
            <!-- Abstract Graphic -->
            <div class="absolute -right-10 -bottom-10 opacity-20 transform rotate-12">
                <span class="material-icons-round text-9xl text-white">fingerprint</span>
            </div>
        </div>
        
        <!-- Expiring Certificates -->
        @if(isset($expiring_certificates) && count($expiring_certificates) > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Expiring Soon</h4>
                <span class="material-icons-round text-amber-500">warning</span>
            </div>
            <div class="space-y-3">
                @foreach($expiring_certificates as $cert)
                <div class="flex items-center p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <span class="material-icons-round text-amber-500 mr-3">schedule</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-white truncate">{{ $cert->product_name }}</p>
                        <p class="text-xs text-amber-600">Expires: {{ \Carbon\Carbon::parse($cert->certificate_valid_until)->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const dashboardStatsUrl = "{{ route('admin.dashboard.stats') }}";
    const statsUrl = "{{ route('admin.dashboard.monitor.stats') }}";
    const feedUrl = "{{ route('admin.dashboard.monitor.feed') }}";
    const feedRoot = document.getElementById('realtime-feed-list');
    const periodButtons = Array.from(document.querySelectorAll('.period-btn'));
    const chartLabels = @json($chart_labels ?? []);
    const chartData = @json($chart_data ?? []);
    const initialPeriod = Number("{{ (int)($period_days ?? 30) }}") || 30;
    let activePeriod = initialPeriod;
    let scanChart = null;

    const setPeriodButtonState = (period) => {
        periodButtons.forEach((btn) => {
            const value = Number(btn.dataset.period || 30);
            const isActive = value === period;
            btn.classList.toggle('bg-primary', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('text-slate-500', !isActive);
        });
    };

    const updateKpi = (id, value) => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = Number(value || 0).toLocaleString();
        }
    };

    const renderChart = (labels = [], data = []) => {
        const canvas = document.getElementById('scanActivityChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(0, 187, 194, 0.18)');
        gradient.addColorStop(1, 'rgba(0, 187, 194, 0)');

        if (scanChart) {
            scanChart.destroy();
        }

        scanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data,
                    borderColor: '#00bbc2',
                    backgroundColor: gradient,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 0,
                    tension: 0.35,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: activePeriod === 365 ? 12 : 10 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.12)' },
                        ticks: { color: '#94a3b8', precision: 0 }
                    }
                }
            }
        });
    };

    const refreshDashboardByPeriod = async (period) => {
        try {
            const res = await fetch(`${dashboardStatsUrl}?period=${period}`);
            const json = await res.json();
            if (!json?.success) return;

            const stats = json.stats || {};
            updateKpi('totalKategori-value', stats.totalKategori);
            updateKpi('totalProduk-value', stats.totalProduk);
            updateKpi('totalUsers-value', stats.totalUsers);
            updateKpi('totalScan-value', stats.totalScan);
            renderChart(json.chart?.labels || [], json.chart?.data || []);
        } catch (error) {
            console.warn('Failed loading dashboard period data', error);
        }
    };

    periodButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const period = Number(button.dataset.period || 30);
            activePeriod = period;
            setPeriodButtonState(period);
            refreshDashboardByPeriod(period);
        });
    });

    const renderFeed = (items = []) => {
        if (!feedRoot) return;
        if (!Array.isArray(items) || items.length === 0) {
            feedRoot.innerHTML = '<div class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada activity event.</div>';
            return;
        }
        feedRoot.innerHTML = items.slice(0, 20).map(item => {
            const statusClass = (item.status || '') === 'success' ? 'text-emerald-600' : 'text-amber-600';
            const summary = item.summary || item.event_type || 'activity';
            const user = item.user_name || 'Guest';
            const type = item.event_type || '-';
            const created = item.created_at || '-';
            return `
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">${summary}</p>
                        <span class="text-[10px] font-bold uppercase ${statusClass}">${item.status || 'info'}</span>
                    </div>
                    <p class="text-xs text-slate-500">${user} · ${type} · ${created}</p>
                </div>
            `;
        }).join('');
    };

    const updateStats = (data = {}) => {
        const set = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = Number(value || 0).toLocaleString();
        };
        set('monitorExternalScans', data.total_external_scans);
        set('monitorSkincare', data.total_skincare_analyses);
        set('monitorInteractions', data.total_interaction_checks);
        set('monitorMajorContra', data.major_or_contra_count);
        set('monitorRiskChecks', data.total_risk_checks);
        set('monitorDrugFoodConflicts', data.total_drug_food_conflicts);
    };

    const poll = async () => {
        try {
            const [statsRes, feedRes] = await Promise.all([fetch(statsUrl), fetch(feedUrl)]);
            const statsJson = await statsRes.json();
            const feedJson = await feedRes.json();
            if (statsJson?.success) updateStats(statsJson.data);
            if (feedJson?.success) renderFeed(feedJson.data);
        } catch (e) {
            console.warn('Monitor polling failed', e);
        }
    };

    // Try Firebase realtime listener first (if firebase SDK/config already loaded in layout)
    try {
        if (window.firebase && window.firebase.database && window.firebase.apps?.length) {
            const db = window.firebase.database();
            db.ref('admin/activity_feed/latest').on('value', snapshot => {
                const latest = snapshot.val();
                if (!latest) return;
                // Fast refresh by polling once to keep ordering/format consistent
                poll();
            });
            db.ref('admin/dashboard/stats').on('value', snapshot => {
                const stats = snapshot.val();
                if (stats) updateStats(stats);
            });
        }
    } catch (e) {
        console.warn('Firebase listener unavailable, using polling fallback', e);
    }

    // Polling fallback / default refresh
    setPeriodButtonState(initialPeriod);
    renderChart(chartLabels, chartData);
    poll();
    setInterval(poll, 12000);
    setInterval(() => refreshDashboardByPeriod(activePeriod), 20000);
})();
</script>
@endpush
