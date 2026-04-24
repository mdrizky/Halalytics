@extends('admin.layouts.admin_layout')

@section('title', 'Dashboard - Halalytics Admin')
@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-current', 'Overview')

@section('content')
<!-- Page Title & Metrics -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Main Dashboard & Analytics</h2>
        <p class="text-slate-500 text-sm mt-1">Platform overview and depth performance analytics.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center space-x-2 bg-white dark:bg-slate-900 p-1.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mr-2">
            <button data-period="30" class="period-btn px-4 py-1.5 text-xs font-bold rounded-lg {{ ($period_days ?? 30) === 30 ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">30 Days</button>
            <button data-period="90" class="period-btn px-4 py-1.5 text-xs font-bold rounded-lg {{ ($period_days ?? 30) === 90 ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">90 Days</button>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.analytics.export', 'users') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <span class="material-icons-round text-lg">file_download</span>
                Export Users
            </a>
            <a href="{{ route('admin.analytics.export', 'scans') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark transition">
                <span class="material-icons-round text-lg">qr_code_scanner</span>
                Export Scans
            </a>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <!-- Card 1: Users -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Total Users</p>
        <div class="flex items-end justify-between mt-1">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['users'] ?? 0) }}</h3>
            <div class="text-primary"><span class="material-icons-round text-lg">groups</span></div>
        </div>
    </div>
    
    <!-- Card 2: New Users Today -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm border-l-4 border-l-emerald-500">
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">New Today</p>
        <div class="flex items-end justify-between mt-1">
            <h3 class="text-2xl font-extrabold text-emerald-600">{{ number_format($analytics['overview']['new_users_today'] ?? 0) }}</h3>
            <div class="text-emerald-500"><span class="material-icons-round text-lg">person_add</span></div>
        </div>
    </div>

    <!-- Card 3: Total Scans -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Total Scans</p>
        <div class="flex items-end justify-between mt-1">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($analytics['overview']['total_scans'] ?? 0) }}</h3>
            <div class="text-orange-500"><span class="material-icons-round text-lg">qr_code_scanner</span></div>
        </div>
    </div>

    <!-- Card 4: Campaigns -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">FCM Sent</p>
        <div class="flex items-end justify-between mt-1">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($analytics['overview']['campaigns_sent'] ?? 0) }}</h3>
            <div class="text-primary"><span class="material-icons-round text-lg">campaign</span></div>
        </div>
    </div>

    <!-- Card 5: Local Products -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Local DB</p>
        <div class="flex items-end justify-between mt-1">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['local_products'] ?? 0) }}</h3>
            <div class="text-blue-500"><span class="material-icons-round text-lg">storage</span></div>
        </div>
    </div>

    <!-- Card 6: Article Published -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">Articles</p>
        <div class="flex items-end justify-between mt-1">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($analytics['article_stats']['published'] ?? 0) }}</h3>
            <div class="text-emerald-500"><span class="material-icons-round text-lg">article</span></div>
        </div>
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

<!-- Analytics Charts Row 1: Growth & Halal -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Pertumbuhan User</h3>
                <p class="text-xs text-slate-500 mt-1">Data 30 hari terakhir</p>
            </div>
            <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-lg">
                <span class="material-icons-round text-lg">trending_up</span>
            </div>
        </div>
        <div class="h-64">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Distribusi Status Halal</h3>
                <p class="text-xs text-slate-500 mt-1">Berdasarkan hasil scan terbaru</p>
            </div>
            <div class="p-2 bg-primary/10 text-primary rounded-lg">
                <span class="material-icons-round text-lg">pie_chart</span>
            </div>
        </div>
        <div class="h-64 flex items-center justify-center">
            <div class="w-full h-full relative">
                <canvas id="halalStatusChartUnified"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Charts Row 2: Scan Activity & Health Trends -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Aktivitas Scan 7 Hari</h3>
                <p class="text-xs text-slate-500 mt-1">Perincian status per hari</p>
            </div>
            <div class="p-2 bg-primary/10 text-primary rounded-lg">
                <span class="material-icons-round text-lg">bar_chart</span>
            </div>
        </div>
        <div class="h-64">
            <canvas id="scanActivityStackedChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Tren Health Tracking 30 Hari</h3>
                <p class="text-xs text-slate-500 mt-1">Metrik kesehatan yang sering dicek</p>
            </div>
            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-lg">
                <span class="material-icons-round text-lg">monitor_heart</span>
            </div>
        </div>
        <div class="h-64">
            <canvas id="healthTrendsChart"></canvas>
        </div>
    </div>
</div>


<!-- Main Grid Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Live Feed -->
    <div class="lg:col-span-2 space-y-6">
        
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
                                    @if(optional($scan)->image)
                                        <img src="{{ $scan->image }}" alt="{{ optional($scan)->product_name ?? 'Product' }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="material-icons-round text-sm text-slate-400" style="display:none">fastfood</span>
                                        @else
                                        <span class="material-icons-round text-sm text-slate-400">fastfood</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium">{{ $scan->product_name ?? 'Unknown Product' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ optional(optional($scan)->user)->username ?? optional(optional($scan)->user)->full_name ?? 'Guest' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $status = strtolower(optional($scan)->status_halal ?? 'unknown');
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
                            <td class="px-6 py-4 text-xs text-slate-400">{{ optional(optional($scan)->created_at)->diffForHumans() ?? '-' }}</td>
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
            <div id="realtime-feed-list" class="divide-y divide-slate-100 dark:divide-slate-800 h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($activity_feed ?? [] as $event)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ optional($event)->summary ?? optional($event)->event_type }}</p>
                        <span class="text-[10px] font-bold uppercase {{ (optional($event)->status ?? '') === 'success' ? 'text-emerald-600' : 'text-amber-600' }}">{{ optional($event)->status ?? 'info' }}</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ optional($event)->user_name ?? 'Guest' }} · {{ optional($event)->event_type ?? '-' }} · {{ optional($event)->created_at ? \Carbon\Carbon::parse(optional($event)->created_at)->diffForHumans() : '-' }}
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
                        @php
                            $imgSrc = optional($product)->image;
                            if (empty($imgSrc) || $imgSrc == 'default.png') {
                                // Generate placeholder image from product name
                                $nameParts = explode(' ', optional($product)->product_name ?? 'Product');
                                $initials = substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
                                $imgSrc = 'https://ui-avatars.com/api/?name=' . urlencode(strtoupper($initials)) . '&background=random&color=fff&size=128&font-size=0.4';
                            }
                        @endphp
                        <img src="{{ $imgSrc }}" alt="{{ optional($product)->product_name }}" class="w-10 h-10 object-cover rounded-lg shadow-sm border border-slate-200 dark:border-slate-700" onerror="this.src='https://ui-avatars.com/api/?name=NA&background=e2e8f0&color=64748b';">
                        <div class="absolute -top-2 -left-2 w-6 h-6 {{ $index === 0 ? 'bg-primary text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }} text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">#{{ $index + 1 }}</div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ Str::limit(optional($product)->product_name, 20) }}</p>
                        <div class="flex items-center text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">
                            @php
                                $topStatus = strtolower((string) (optional($product)->halal_status ?? 'pending'));
                                $topStatusClass = match ($topStatus) {
                                    'halal' => 'text-emerald-600',
                                    'haram', 'tidak halal' => 'text-red-600',
                                    'syubhat', 'diragukan' => 'text-amber-600',
                                    default => 'text-slate-500',
                                };
                            @endphp
                            <span class="{{ $topStatusClass }}">{{ optional($product)->halal_status ?? 'Pending' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $product->category_name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $scanCount = optional($product)->scan_count ?? 0;
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
    // Unified Data from Controller
    const userGrowthRaw = @json($analytics['user_growth'] ?? []);
    const scanActivityRaw = @json($analytics['scan_activity'] ?? []);
    const halalStatsDetailed = @json($analytics['halal_stats_detailed'] ?? []);
    const healthTrendsRaw = @json($analytics['health_trends'] ?? []);

    const renderUnifiedCharts = () => {
        // 1. User Growth Chart (Line)
        const growthCtx = document.getElementById('userGrowthChart')?.getContext('2d');
        if (growthCtx) {
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: userGrowthRaw.map(d => d.date),
                    datasets: [{
                        label: 'User Baru',
                        data: userGrowthRaw.map(d => d.count),
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // 2. Detailed Halal Donut
        const halalCtx = document.getElementById('halalStatusChartUnified')?.getContext('2d');
        if (halalCtx) {
            new Chart(halalCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Halal', 'Haram', 'Syubhat'],
                    datasets: [{
                        data: [halalStatsDetailed.halal, halalStatsDetailed.haram, halalStatsDetailed.syubhat],
                        backgroundColor: ['#059669', '#dc2626', '#d97706'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // 3. Scan Activity Stacked Bar
        const scanCtx = document.getElementById('scanActivityStackedChart')?.getContext('2d');
        if (scanCtx) {
            new Chart(scanCtx, {
                type: 'bar',
                data: {
                    labels: scanActivityRaw.map(d => d.date),
                    datasets: [
                        { label: 'Halal', data: scanActivityRaw.map(d => d.halal), backgroundColor: '#059669' },
                        { label: 'Syubhat', data: scanActivityRaw.map(d => d.syubhat), backgroundColor: '#d97706' },
                        { label: 'Haram', data: scanActivityRaw.map(d => d.haram), backgroundColor: '#dc2626' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { stacked: true }, y: { stacked: true } }
                }
            });
        }

        // 4. Health Trends Horizontal Bar
        const healthCtx = document.getElementById('healthTrendsChart')?.getContext('2d');
        if (healthCtx) {
            new Chart(healthCtx, {
                type: 'bar',
                data: {
                    labels: healthTrendsRaw.map(d => d.metric_type),
                    datasets: [{
                        data: healthTrendsRaw.map(d => d.count),
                        backgroundColor: '#8b5cf6',
                        borderRadius: 10
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    };

    renderUnifiedCharts();
    
    // Auto-scroll activity feed to top when new items arrive if needed
    const feedList = document.getElementById('realtime-feed-list');
    if (feedList) {
        // Simple polling for demo/fallback - usually handled via Pusher/Reverb
        setInterval(() => {
            // Logic to fetch new feed if any
        }, 30000);
    }
</script>
@endpush
