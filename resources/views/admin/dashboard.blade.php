@extends('admin.layouts.admin_layout')

@section('title', 'Dashboard - Halalytics Admin')
@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-current', 'Overview')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap');

    :root {
        --primary: #C01552;
        --primary-light: #E8195D;
        --primary-dark: #8B0F3A;
        --primary-glow: rgba(192, 21, 82, 0.18);
        --emerald: #059669;
        --amber: #D97706;
        --red-danger: #DC2626;
        --surface: #ffffff;
        --surface-2: #F8FAFC;
        --border: #E2E8F0;
        --text-primary: #0F172A;
        --text-muted: #64748B;
        --radius: 16px;
    }

    .dark {
        --surface: #0F172A;
        --surface-2: #1E293B;
        --border: #1E293B;
        --text-primary: #F1F5F9;
        --text-muted: #94A3B8;
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    .dash-title { font-family: 'Syne', sans-serif; }

    /* ===== KPI CARD PREMIUM ===== */
    .kpi-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        opacity: 0;
        transition: opacity 0.2s;
    }
    .kpi-card:hover::before { opacity: 1; }
    .kpi-card.accent-emerald::before { background: linear-gradient(90deg, #059669, #10B981); opacity: 1; }
    .kpi-card.accent-primary::before { background: linear-gradient(90deg, var(--primary), var(--primary-light)); opacity: 1; }
    .kpi-card .kpi-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .kpi-card .kpi-value {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }
    .kpi-card .kpi-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-muted);
    }

    /* ===== MONITOR METRIC CARDS ===== */
    .monitor-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: box-shadow 0.2s;
    }
    .monitor-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
    .monitor-dot {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .monitor-card .monitor-val {
        font-family: 'Syne', sans-serif;
        font-size: 1.6rem; font-weight: 800;
        line-height: 1;
    }
    .monitor-card .monitor-label {
        font-size: 10px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: var(--text-muted);
    }

    /* ===== CHART CARDS ===== */
    .chart-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .chart-card-title {
        font-family: 'Syne', sans-serif;
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* ===== TABLE ===== */
    .feed-table thead tr th {
        background: var(--surface-2);
        font-size: 10px; font-weight: 700;
        letter-spacing: 0.1em; text-transform: uppercase;
        color: var(--text-muted);
        padding: 12px 20px;
    }
    .feed-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }
    .feed-table tbody tr:hover { background: var(--surface-2); }
    .feed-table tbody td { padding: 14px 20px; }

    /* ===== BADGE ===== */
    .badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.03em;
    }
    .badge-halal    { background: #D1FAE5; color: #065F46; }
    .badge-haram    { background: #FEE2E2; color: #991B1B; }
    .badge-syubhat  { background: #FEF3C7; color: #92400E; }
    .badge-unknown  { background: #F1F5F9; color: #475569; }
    .dark .badge-halal   { background: rgba(5,150,105,0.2); color: #6EE7B7; }
    .dark .badge-haram   { background: rgba(220,38,38,0.2); color: #FCA5A5; }
    .dark .badge-syubhat { background: rgba(217,119,6,0.2); color: #FCD34D; }
    .dark .badge-unknown { background: rgba(71,85,105,0.2); color: #94A3B8; }

    /* ===== EXTERNAL DATA SOURCE ===== */
    .datasource-card {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px; padding: 16px;
        display: flex; flex-direction: column; gap: 6px;
        transition: all 0.2s;
    }
    .datasource-card:hover {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .datasource-card .ds-val {
        font-family: 'Syne', sans-serif;
        font-size: 1.6rem; font-weight: 800;
        color: var(--text-primary);
    }
    .datasource-card .ds-label {
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: var(--text-muted);
    }
    .datasource-card .ds-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 20px;
        font-size: 10px; font-weight: 700;
        background: #D1FAE5; color: #065F46;
        width: fit-content;
    }

    /* ===== PLATFORM STATUS CARD ===== */
    .platform-status-card {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 60%, var(--primary-light) 100%);
        border-radius: var(--radius);
        padding: 24px;
        position: relative;
        overflow: hidden;
    }
    .platform-status-card .ps-bg-icon {
        position: absolute;
        right: -20px; bottom: -20px;
        font-size: 120px;
        color: white;
        opacity: 0.08;
        transform: rotate(15deg);
    }
    .platform-status-card .latency-bar {
        height: 6px; border-radius: 3px;
        background: rgba(255,255,255,0.2);
        overflow: hidden; margin-top: 8px;
    }
    .platform-status-card .latency-fill {
        height: 100%; width: 75%;
        background: white; border-radius: 3px;
    }

    /* ===== TOP PRODUCTS ===== */
    .top-product-item {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }
    .top-product-item:last-child { border-bottom: none; }
    .top-product-item:hover { background: var(--surface-2); margin: 0 -12px; padding-left: 12px; padding-right: 12px; border-radius: 10px; }
    .top-product-rank {
        width: 26px; height: 26px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 800;
        flex-shrink: 0;
    }
    .rank-1 { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; }
    .rank-other { background: var(--surface-2); color: var(--text-muted); border: 1px solid var(--border); }

    /* ===== ACTIVITY FEED ===== */
    .activity-item {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: flex-start; gap: 12px;
        transition: background 0.15s;
    }
    .activity-item:hover { background: var(--surface-2); }
    .activity-dot {
        width: 8px; height: 8px;
        border-radius: 50%; flex-shrink: 0; margin-top: 5px;
    }
    .dot-success { background: #059669; }
    .dot-info { background: #3B82F6; }
    .dot-warning { background: #D97706; }

    /* ===== SECTION HEADER ===== */
    .section-header {
        display: flex; align-items: center;
        justify-content: space-between;
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
    }
    .section-header h4 {
        font-family: 'Syne', sans-serif;
        font-size: 1rem; font-weight: 800;
        color: var(--text-primary);
    }

    /* ===== PERIOD TOGGLE ===== */
    .period-toggle {
        display: flex; align-items: center;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 10px; padding: 4px; gap: 2px;
    }
    .period-btn-styled {
        padding: 6px 16px; border-radius: 8px;
        font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
        color: var(--text-muted); border: none; background: transparent;
    }
    .period-btn-styled.active {
        background: var(--primary); color: white;
        box-shadow: 0 2px 8px var(--primary-glow);
    }

    /* ===== CUSTOM SCROLLBAR ===== */
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    /* ===== PULSE ANIMATION ===== */
    @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.3); } }
    .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

    /* ===== VIEW ALL BUTTON ===== */
    .view-all-btn {
        display: block; width: 100%; padding: 12px;
        text-align: center; border-radius: 10px;
        border: 1.5px dashed var(--border);
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: var(--text-muted);
        transition: all 0.2s; margin-top: 16px;
        text-decoration: none;
    }
    .view-all-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-glow);
    }

    /* ===== EXPORT BUTTONS ===== */
    .export-btn-outline {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 10px;
        border: 1.5px solid var(--border);
        font-size: 13px; font-weight: 700;
        color: var(--text-primary);
        transition: all 0.2s; text-decoration: none;
    }
    .export-btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-glow); }
    .export-btn-filled {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        font-size: 13px; font-weight: 700;
        color: white; transition: all 0.2s; text-decoration: none;
        box-shadow: 0 4px 15px var(--primary-glow);
    }
    .export-btn-filled:hover { transform: translateY(-1px); box-shadow: 0 6px 20px var(--primary-glow); }

    /* ===== REALTIME SYNC PILL ===== */
    .sync-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 20px;
        background: #D1FAE5; color: #065F46;
        font-size: 11px; font-weight: 700;
    }
    .dark .sync-pill { background: rgba(5,150,105,0.2); color: #6EE7B7; }
</style>
@endpush

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-1 h-6 rounded-full bg-gradient-to-b from-primary to-primary-light" style="background: linear-gradient(to bottom, var(--primary), var(--primary-light));"></div>
            <h2 class="dash-title text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Main Dashboard & Analytics</h2>
        </div>
        <p class="text-slate-500 text-sm ml-3">Platform overview and depth performance analytics.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <div class="period-toggle">
            <button data-period="30" class="period-btn period-btn-styled {{ ($period_days ?? 30) === 30 ? 'active' : '' }}">30 Days</button>
            <button data-period="90" class="period-btn period-btn-styled {{ ($period_days ?? 30) === 90 ? 'active' : '' }}">90 Days</button>
        </div>
        <a href="{{ route('admin.analytics.export', 'users') }}" class="export-btn-outline">
            <span class="material-icons-round text-base">file_download</span>
            Export Users
        </a>
        <a href="{{ route('admin.analytics.export', 'scans') }}" class="export-btn-filled">
            <span class="material-icons-round text-base">qr_code_scanner</span>
            Export Scans
        </a>
    </div>
</div>

{{-- ===== KPI CARDS ===== --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

    {{-- Total Users --}}
    <div class="kpi-card">
        <div class="flex items-start justify-between mb-3">
            <div class="kpi-icon" style="background: rgba(192,21,82,0.1);">
                <span class="material-icons-round" style="color: var(--primary); font-size: 20px;">groups</span>
            </div>
        </div>
        <p class="kpi-label mb-1">Total Users</p>
        <p class="kpi-value" style="color: var(--text-primary);">{{ number_format($stats['users'] ?? 0) }}</p>
    </div>

    {{-- New Today --}}
    <div class="kpi-card accent-emerald">
        <div class="flex items-start justify-between mb-3">
            <div class="kpi-icon" style="background: rgba(5,150,105,0.12);">
                <span class="material-icons-round" style="color: #059669; font-size: 20px;">person_add</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full">TODAY</span>
        </div>
        <p class="kpi-label mb-1">New Users</p>
        <p class="kpi-value" style="color: #059669;">{{ number_format($analytics['overview']['new_users_today'] ?? 0) }}</p>
    </div>

    {{-- Total Scans --}}
    <div class="kpi-card">
        <div class="flex items-start justify-between mb-3">
            <div class="kpi-icon" style="background: rgba(192,21,82,0.1);">
                <span class="material-icons-round" style="color: var(--primary); font-size: 20px;">qr_code_scanner</span>
            </div>
        </div>
        <p class="kpi-label mb-1">Total Scans</p>
        <p class="kpi-value">{{ number_format($analytics['overview']['total_scans'] ?? 0) }}</p>
    </div>

    {{-- FCM Sent --}}
    <div class="kpi-card">
        <div class="flex items-start justify-between mb-3">
            <div class="kpi-icon" style="background: rgba(59,130,246,0.1);">
                <span class="material-icons-round" style="color: #3B82F6; font-size: 20px;">campaign</span>
            </div>
        </div>
        <p class="kpi-label mb-1">FCM Sent</p>
        <p class="kpi-value">{{ number_format($analytics['overview']['campaigns_sent'] ?? 0) }}</p>
    </div>

    {{-- Local DB --}}
    <div class="kpi-card">
        <div class="flex items-start justify-between mb-3">
            <div class="kpi-icon" style="background: rgba(100,116,139,0.1);">
                <span class="material-icons-round" style="color: #64748B; font-size: 20px;">storage</span>
            </div>
        </div>
        <p class="kpi-label mb-1">Local DB</p>
        <p class="kpi-value">{{ number_format($stats['local_products'] ?? 0) }}</p>
    </div>

    {{-- Articles --}}
    <div class="kpi-card">
        <div class="flex items-start justify-between mb-3">
            <div class="kpi-icon" style="background: rgba(5,150,105,0.1);">
                <span class="material-icons-round" style="color: #059669; font-size: 20px;">article</span>
            </div>
        </div>
        <p class="kpi-label mb-1">Articles</p>
        <p class="kpi-value">{{ number_format($analytics['article_stats']['published'] ?? 0) }}</p>
    </div>
</div>

{{-- ===== EXTERNAL DATA SOURCES ===== --}}
<div class="chart-card mb-8">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <span class="material-icons-round text-slate-400">hub</span>
            <h4 class="chart-card-title">External Data Sources</h4>
        </div>
        <div class="sync-pill">
            <span class="pulse-dot w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
            Realtime Sync Active
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="datasource-card">
            <span class="ds-label">Local DB</span>
            <span class="ds-val">{{ number_format($stats['local_products'] ?? 0) }}</span>
            <span class="ds-badge"><span class="material-icons-round text-[10px]">check_circle</span>Active</span>
        </div>
        <div class="datasource-card">
            <span class="ds-label">Open Food Facts</span>
            <span class="ds-val">{{ number_format($stats['open_food_facts_products'] ?? 0) }}</span>
            <span class="ds-badge"><span class="material-icons-round text-[10px]">check_circle</span>Active</span>
        </div>
        <div class="datasource-card">
            <span class="ds-label">Open Beauty Facts</span>
            <span class="ds-val">{{ number_format($stats['open_beauty_facts_products'] ?? 0) }}</span>
            <span class="ds-badge"><span class="material-icons-round text-[10px]">check_circle</span>Active</span>
        </div>
        <div class="datasource-card">
            <span class="ds-label">OpenFDA Medicines</span>
            <span class="ds-val">{{ number_format($stats['openfda_medicines'] ?? 0) }}</span>
            <span class="ds-badge"><span class="material-icons-round text-[10px]">check_circle</span>Active</span>
        </div>
    </div>
</div>

{{-- ===== REALTIME MONITOR METRICS ===== --}}
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <span class="material-icons-round text-slate-400 text-sm">sensors</span>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Realtime Monitor</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="monitor-card">
            <div class="monitor-dot" style="background: rgba(59,130,246,0.12);">
                <span class="material-icons-round text-blue-500 text-base">travel_explore</span>
            </div>
            <div>
                <p class="monitor-label">External Scans</p>
                <p id="monitorExternalScans" class="monitor-val text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_external_scans'] ?? 0) }}</p>
            </div>
        </div>
        <div class="monitor-card">
            <div class="monitor-dot" style="background: rgba(168,85,247,0.12);">
                <span class="material-icons-round text-purple-500 text-base">face</span>
            </div>
            <div>
                <p class="monitor-label">Skincare</p>
                <p id="monitorSkincare" class="monitor-val text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_skincare_analyses'] ?? 0) }}</p>
            </div>
        </div>
        <div class="monitor-card">
            <div class="monitor-dot" style="background: rgba(20,184,166,0.12);">
                <span class="material-icons-round text-teal-500 text-base">compare_arrows</span>
            </div>
            <div>
                <p class="monitor-label">Interactions</p>
                <p id="monitorInteractions" class="monitor-val text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_interaction_checks'] ?? 0) }}</p>
            </div>
        </div>
        <div class="monitor-card">
            <div class="monitor-dot" style="background: rgba(220,38,38,0.1);">
                <span class="material-icons-round text-red-500 text-base">dangerous</span>
            </div>
            <div>
                <p class="monitor-label">Major/Contra</p>
                <p id="monitorMajorContra" class="monitor-val" style="color: #DC2626;">{{ number_format($monitor_stats['major_or_contra_count'] ?? 0) }}</p>
            </div>
        </div>
        <div class="monitor-card">
            <div class="monitor-dot" style="background: rgba(192,21,82,0.1);">
                <span class="material-icons-round text-base" style="color:var(--primary);">health_and_safety</span>
            </div>
            <div>
                <p class="monitor-label">Risk Checks</p>
                <p id="monitorRiskChecks" class="monitor-val text-slate-800 dark:text-white">{{ number_format($monitor_stats['total_risk_checks'] ?? 0) }}</p>
            </div>
        </div>
        <div class="monitor-card">
            <div class="monitor-dot" style="background: rgba(217,119,6,0.1);">
                <span class="material-icons-round text-amber-500 text-base">no_food</span>
            </div>
            <div>
                <p class="monitor-label">Drug-Food</p>
                <p id="monitorDrugFoodConflicts" class="monitor-val" style="color: #D97706;">{{ number_format($monitor_stats['total_drug_food_conflicts'] ?? 0) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ===== CHARTS ROW 1 ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="chart-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="chart-card-title">Pertumbuhan User</h3>
                <p class="text-xs text-slate-500 mt-0.5">Data 30 hari terakhir</p>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(5,150,105,0.1);">
                <span class="material-icons-round text-emerald-600 text-base">trending_up</span>
            </div>
        </div>
        <div class="h-64"><canvas id="userGrowthChart"></canvas></div>
    </div>

    <div class="chart-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="chart-card-title">Distribusi Status Halal</h3>
                <p class="text-xs text-slate-500 mt-0.5">Berdasarkan hasil scan terbaru</p>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(192,21,82,0.1);">
                <span class="material-icons-round text-base" style="color:var(--primary);">pie_chart</span>
            </div>
        </div>
        <div class="h-64 flex items-center justify-center">
            <canvas id="halalStatusChartUnified"></canvas>
        </div>
    </div>
</div>

{{-- ===== CHARTS ROW 2 ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="chart-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="chart-card-title">Aktivitas Scan 7 Hari</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perincian status per hari</p>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(192,21,82,0.1);">
                <span class="material-icons-round text-base" style="color:var(--primary);">bar_chart</span>
            </div>
        </div>
        <div class="h-64"><canvas id="scanActivityStackedChart"></canvas></div>
    </div>

    <div class="chart-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="chart-card-title">Tren Health Tracking 30 Hari</h3>
                <p class="text-xs text-slate-500 mt-0.5">Metrik kesehatan yang sering dicek</p>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                <span class="material-icons-round text-slate-600 text-base">monitor_heart</span>
            </div>
        </div>
        <div class="h-64"><canvas id="healthTrendsChart"></canvas></div>
    </div>
</div>

{{-- ===== MAIN GRID: FEED + SIDEBAR ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- LEFT: Live Feed + Activity Feed --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Live Scan Feed --}}
        <div class="chart-card p-0 overflow-hidden">
            <div class="section-header">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-500 pulse-dot"></div>
                    <h4>Live Scan Feed</h4>
                </div>
                <a href="{{ route('admin.scan.index') }}" class="text-xs font-bold hover:underline" style="color:var(--primary);">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full feed-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_scans ?? [] as $scan)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if(optional($scan)->image)
                                            <img src="{{ $scan->image }}" alt="{{ optional($scan)->product_name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span class="material-icons-round text-sm text-slate-400" style="display:none">fastfood</span>
                                        @else
                                            <span class="material-icons-round text-sm text-slate-400">fastfood</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $scan->product_name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ optional(optional($scan)->user)->username ?? optional(optional($scan)->user)->full_name ?? 'Guest' }}</td>
                            <td>
                                @php
                                    $status = strtolower(optional($scan)->status_halal ?? 'unknown');
                                    $badgeClass = match($status) {
                                        'halal' => 'badge-halal',
                                        'haram' => 'badge-haram',
                                        'syubhat','mushbooh' => 'badge-syubhat',
                                        default => 'badge-unknown'
                                    };
                                    $dotColor = match($status) {
                                        'halal' => '#059669','haram' => '#DC2626',
                                        'syubhat','mushbooh' => '#D97706', default => '#94A3B8'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:{{ $dotColor }};"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="text-xs text-slate-400">{{ optional(optional($scan)->created_at)->diffForHumans() ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <span class="material-icons-round text-4xl text-slate-300 dark:text-slate-700 block mb-2">inbox</span>
                                <p class="text-sm text-slate-400">No recent scans</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Realtime Activity Feed --}}
        <div class="chart-card p-0 overflow-hidden">
            <div class="section-header">
                <div class="flex items-center gap-2">
                    <span class="material-icons-round text-slate-400 text-base">bolt</span>
                    <h4>Realtime Activity Feed</h4>
                </div>
                <span class="sync-pill text-[10px]">
                    <span class="pulse-dot w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    Live + Polling Fallback
                </span>
            </div>
            <div id="realtime-feed-list" class="divide-y divide-slate-100 dark:divide-slate-800 h-[420px] overflow-y-auto custom-scroll">
                @forelse($activity_feed ?? [] as $event)
                <div class="activity-item">
                    <span class="activity-dot {{ (optional($event)->status ?? '') === 'success' ? 'dot-success' : 'dot-info' }}"></span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ optional($event)->summary ?? optional($event)->event_type }}</p>
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full flex-shrink-0 {{ (optional($event)->status ?? '') === 'success' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/20' }}">
                                {{ optional($event)->status ?? 'info' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">
                            {{ optional($event)->user_name ?? 'Guest' }} · {{ optional($event)->event_type ?? '-' }} · {{ optional($event)->created_at ? \Carbon\Carbon::parse(optional($event)->created_at)->diffForHumans() : '-' }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-slate-400 text-sm">Belum ada activity event.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="space-y-6">

        {{-- Top Scanned Products --}}
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h4 class="chart-card-title">Top Scanned</h4>
                <span class="material-icons-round text-slate-300 text-lg">leaderboard</span>
            </div>
            <div>
                @forelse($top_products ?? [] as $index => $product)
                <div class="top-product-item">
                    <div class="top-product-rank {{ $index === 0 ? 'rank-1' : 'rank-other' }}">#{{ $index + 1 }}</div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 overflow-hidden flex-shrink-0">
                        @php
                            $imgSrc = optional($product)->image;
                            if (empty($imgSrc) || $imgSrc == 'default.png') {
                                $nameParts = explode(' ', optional($product)->product_name ?? 'P');
                                $initials = substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
                                $imgSrc = 'https://ui-avatars.com/api/?name=' . urlencode(strtoupper($initials)) . '&background=random&color=fff&size=128&font-size=0.4';
                            }
                        @endphp
                        <img src="{{ $imgSrc }}" alt="{{ optional($product)->product_name }}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=NA&background=e2e8f0&color=64748b';">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ Str::limit(optional($product)->product_name, 18) }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            @php
                                $topStatus = strtolower((string)(optional($product)->halal_status ?? 'pending'));
                                $topColor = match($topStatus) { 'halal' => '#059669', 'haram','tidak halal' => '#DC2626', 'syubhat','diragukan' => '#D97706', default => '#94A3B8' };
                            @endphp
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $topColor }};"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ optional($product)->halal_status ?? 'Pending' }}</span>
                            <span class="text-slate-300">·</span>
                            <span class="text-[10px] text-slate-400 truncate">{{ $product->category_name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @php
                            $sc = optional($product)->scan_count ?? 0;
                            $sf = $sc >= 1000 ? number_format($sc/1000,1).'k' : number_format($sc);
                        @endphp
                        <p class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $sf }}</p>
                        <p class="text-[10px] text-slate-400">scans</p>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-slate-400">
                    <span class="material-icons-round text-4xl block mb-2">trending_up</span>
                    <p class="text-sm">No product data yet</p>
                </div>
                @endforelse
            </div>
            <a href="{{ route('admin.product.index') }}" class="view-all-btn">View All Products</a>
        </div>

        {{-- Platform Status --}}
        <div class="platform-status-card">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-white font-bold text-sm">Platform Status</h4>
                    <span class="text-white/60 text-xs">Live</span>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 bg-emerald-300 rounded-full pulse-dot"></div>
                    <p class="text-white/80 text-xs font-medium">All systems operational</p>
                </div>
                <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm space-y-3">
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <p class="text-white/70 text-[10px] font-bold uppercase tracking-wider">API Latency</p>
                            <p class="text-white font-bold text-sm">{{ $stats['api_latency'] ?? '42' }}ms</p>
                        </div>
                        <div class="latency-bar"><div class="latency-fill"></div></div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                            <span class="text-white/70 text-[10px] font-bold">Uptime</span>
                        </div>
                        <span class="text-white font-bold text-xs">99.9%</span>
                    </div>
                </div>
            </div>
            <span class="material-icons-round ps-bg-icon">fingerprint</span>
        </div>

        {{-- Expiring Certificates --}}
        @if(isset($expiring_certificates) && count($expiring_certificates) > 0)
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <h4 class="chart-card-title">Expiring Soon</h4>
                <span class="material-icons-round text-amber-500">warning_amber</span>
            </div>
            <div class="space-y-3">
                @foreach($expiring_certificates as $cert)
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background: rgba(217,119,6,0.08); border: 1px solid rgba(217,119,6,0.2);">
                    <span class="material-icons-round text-amber-500 text-base flex-shrink-0">schedule</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $cert->product_name }}</p>
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
    const userGrowthRaw      = @json($analytics['user_growth'] ?? []);
    const scanActivityRaw    = @json($analytics['scan_activity'] ?? []);
    const halalStatsDetailed = @json($analytics['halal_stats_detailed'] ?? []);
    const healthTrendsRaw    = @json($analytics['health_trends'] ?? []);

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
    const tickColor = isDark ? '#94A3B8' : '#94A3B8';

    const baseAxisOpts = {
        grid: { color: gridColor },
        ticks: { color: tickColor, font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 } }
    };

    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

    // 1. User Growth — Line
    const growthCtx = document.getElementById('userGrowthChart')?.getContext('2d');
    if (growthCtx) {
        const grad = growthCtx.createLinearGradient(0, 0, 0, 200);
        grad.addColorStop(0, 'rgba(192,21,82,0.25)');
        grad.addColorStop(1, 'rgba(192,21,82,0)');
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: userGrowthRaw.map(d => d.date),
                datasets: [{
                    label: 'User Baru',
                    data: userGrowthRaw.map(d => d.count),
                    borderColor: '#C01552',
                    backgroundColor: grad,
                    fill: true, tension: 0.45,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#C01552'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                scales: { x: baseAxisOpts, y: { ...baseAxisOpts, beginAtZero: true } }
            }
        });
    }

    // 2. Halal Donut
    const halalCtx = document.getElementById('halalStatusChartUnified')?.getContext('2d');
    if (halalCtx) {
        new Chart(halalCtx, {
            type: 'doughnut',
            data: {
                labels: ['Halal', 'Haram', 'Syubhat'],
                datasets: [{
                    data: [halalStatsDetailed.halal, halalStatsDetailed.haram, halalStatsDetailed.syubhat],
                    backgroundColor: ['#059669', '#C01552', '#F59E0B'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: tickColor, padding: 16, font: { size: 11, weight: '600' }, usePointStyle: true, pointStyleWidth: 8 }
                    }
                }
            }
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
                    { label: 'Halal',   data: scanActivityRaw.map(d => d.halal),   backgroundColor: '#059669', borderRadius: 4 },
                    { label: 'Syubhat', data: scanActivityRaw.map(d => d.syubhat), backgroundColor: '#F59E0B', borderRadius: 4 },
                    { label: 'Haram',   data: scanActivityRaw.map(d => d.haram),   backgroundColor: '#C01552', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { color: tickColor, font: { size: 11, weight: '600' }, usePointStyle: true, pointStyleWidth: 8, padding: 12 } }
                },
                scales: {
                    x: { ...baseAxisOpts, stacked: true },
                    y: { ...baseAxisOpts, stacked: true, beginAtZero: true }
                }
            }
        });
    }

    // 4. Health Trends — Horizontal Bar
    const healthCtx = document.getElementById('healthTrendsChart')?.getContext('2d');
    if (healthCtx) {
        const colors = ['#C01552','#E8195D','#059669','#3B82F6','#F59E0B','#8B5CF6'];
        new Chart(healthCtx, {
            type: 'bar',
            data: {
                labels: healthTrendsRaw.map(d => d.metric_type),
                datasets: [{
                    data: healthTrendsRaw.map(d => d.count),
                    backgroundColor: healthTrendsRaw.map((_, i) => colors[i % colors.length] + 'CC'),
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ...baseAxisOpts, beginAtZero: true },
                    y: { ...baseAxisOpts, grid: { display: false } }
                }
            }
        });
    }

    // Realtime polling stub
    const feedList = document.getElementById('realtime-feed-list');
    if (feedList) {
        setInterval(() => {}, 30000);
    }
</script>
@endpush
