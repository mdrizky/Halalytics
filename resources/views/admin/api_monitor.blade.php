@extends('admin.master')

@section('title', 'API Integration Monitor | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">API Monitor</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--text-main);">API Connectivity Hub</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Monitor real-time health and latency of all external service integrations.</p>
    </div>
    <form action="{{ route('admin.api-monitor.check') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-heartbeat"></i> Trigger Health Check
        </button>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 24px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<!-- API Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
    @foreach($apis as $api)
    @php 
        $data = $current[$api] ?? ['status' => 'unknown', 'latency_ms' => 0, 'uptime_24h' => 0, 'uptime_7d' => 0, 'last_check' => null]; 
        $statusColor = match($data['status']) {
            'up' => '#2D6A4F',
            'slow' => '#F4A261',
            'degraded' => '#E67E22',
            'down' => '#E74C3C',
            default => '#7F8C8D'
        };
    @endphp
    <div class="card" style="border-top: 4px solid {{ $statusColor }};">
        <div class="card-body" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Service</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-top: 4px;">{{ strtoupper($api) }}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 6px; background: {{ $statusColor }}15; color: {{ $statusColor }}; padding: 4px 10px; border-radius: 100px; font-size: 10px; font-weight: 800;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $statusColor }}; {{ $data['status'] == 'up' ? 'animation: pulse 2s infinite;' : '' }}"></span>
                    {{ strtoupper($data['status']) }}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 11px; color: var(--text-muted);">Latency</div>
                    <div style="font-size: 16px; font-weight: 700; color: var(--text-main);">{{ number_format($data['latency_ms']) }}ms</div>
                </div>
                <div>
                    <div style="font-size: 11px; color: var(--text-muted);">Uptime (24h)</div>
                    <div style="font-size: 16px; font-weight: 700; color: {{ $data['uptime_24h'] > 98 ? 'var(--primary-color)' : 'var(--accent-color)' }}">{{ $data['uptime_24h'] }}%</div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 11px; color: var(--text-muted);">
                    <i class="far fa-clock"></i> {{ $data['last_check'] ? \Carbon\Carbon::parse($data['last_check'])->diffForHumans() : 'Never' }}
                </div>
                <a href="{{ route('admin.api-monitor.history', $api) }}" style="font-size: 11px; color: var(--primary-color); font-weight: 700; text-decoration: none;">HISTORY →</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Latency Analysis -->
<div class="card">
    <div class="card-body">
        <h3 style="margin: 0 0 24px; font-size: 18px; color: var(--text-main);">Latency Trend Analysis (24h)</h3>
        <div style="height: 350px; width: 100%;">
            <canvas id="latencyChart"></canvas>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(45, 106, 79, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(45, 106, 79, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(45, 106, 79, 0); }
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
    const historyData = @json($history);
    const colors = { 
        gemini: '#2D6A4F', 
        openfoodfacts: '#F4A261', 
        fda: '#3498DB', 
        openbeautyfacts: '#E74C3C',
        halal_id: '#8E44AD'
    };

    const datasets = Object.entries(historyData).map(([api, logs]) => ({
        label: api.toUpperCase(),
        data: logs.map(l => ({ x: new Date(l.checked_at), y: l.latency_ms })),
        borderColor: colors[api] || '#7F8C8D',
        backgroundColor: (colors[api] || '#7F8C8D') + '10',
        borderWidth: 3,
        pointRadius: 0,
        pointHoverRadius: 4,
        tension: 0.4,
        fill: true,
    }));

    new Chart(document.getElementById('latencyChart'), {
        type: 'line',
        data: { datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { 
                legend: { position: 'top', labels: { usePointStyle: true, padding: 20, font: { weight: 'bold' } } },
                tooltip: { backgroundColor: '#1A1A1A', padding: 12, titleFont: { size: 14 }, bodyFont: { size: 13 } }
            },
            scales: {
                x: { 
                    type: 'time', 
                    time: { unit: 'hour', displayFormats: { hour: 'HH:mm' } }, 
                    grid: { display: false },
                    ticks: { font: { weight: 'bold' } }
                },
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { weight: 'bold' }, callback: (v) => v + 'ms' }
                }
            }
        }
    });
</script>
@endpush
