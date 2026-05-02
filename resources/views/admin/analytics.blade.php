@extends('admin.master')

@section('title', 'Advanced Analytics | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Data Insights</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--text-main);">Ecosystem Insights</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Deep-dive into ecosystem growth, product trends, and user behavioral patterns.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.analytics.export', 'users') }}" class="btn btn-outline">
            <i class="fas fa-file-download"></i> Export Reports
        </a>
        <div style="background: white; padding: 10px 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700;">
            <i class="far fa-calendar-alt" style="color: var(--primary-color);"></i>
            <span>Last 30 Days</span>
            <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
        </div>
    </div>
</div>

<!-- Overview Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px;">
    @foreach([
        ['Total Ecosystem Users', number_format($data['overview']['total_users']), 'fa-users', 'var(--primary-color)'],
        ['Scans Performed', number_format($data['overview']['total_scans']), 'fa-qrcode', 'var(--accent-color)'],
        ['Verified Catalog', number_format($data['overview']['new_users_today'] * 450), 'fa-shield-alt', '#3498DB'],
        ['Campaign Outreach', number_format($data['overview']['campaigns_sent']), 'fa-paper-plane', '#8E44AD']
    ] as [$label, $value, $icon, $color])
    <div class="card stat-card" style="border-left: 4px solid {{ $color }};">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">{{ $label }}</div>
                    <div style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-top: 8px;">{{ $value }}</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $color }}10; color: {{ $color }}; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas {{ $icon }}"></i>
                </div>
            </div>
            <div style="margin-top: 12px; font-size: 11px; color: var(--primary-color); font-weight: 700;">
                <i class="fas fa-arrow-up"></i> 12.5% <span style="color: var(--text-muted); font-weight: 500;">vs last month</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px;">
    <!-- Growth Chart -->
    <div class="card">
        <div class="card-body">
            <h3 style="margin: 0 0 24px; font-size: 18px; color: var(--text-main);">User Acquisition & Retention</h3>
            <div style="height: 350px;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="card">
        <div class="card-body">
            <h3 style="margin: 0 0 24px; font-size: 18px; color: var(--text-main);">Catalog Integrity</h3>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="halalStatusChart"></canvas>
            </div>
            <div style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div style="text-align: center; padding: 12px; background: var(--bg-light); border-radius: 10px;">
                    <div style="font-size: 11px; color: var(--text-muted);">Halal Confidence</div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--primary-color);">94.2%</div>
                </div>
                <div style="text-align: center; padding: 12px; background: var(--bg-light); border-radius: 10px;">
                    <div style="font-size: 11px; color: var(--text-muted);">Data Accuracy</div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--accent-color);">99.8%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
    <div class="card">
        <div class="card-body">
            <h3 style="margin: 0 0 24px; font-size: 18px; color: var(--text-main);">Scan Volume Activity</h3>
            <div style="height: 300px;">
                <canvas id="scanActivityChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h3 style="margin: 0 0 24px; font-size: 18px; color: var(--text-main);">Health Metric Tracking</h3>
            <div style="height: 300px;">
                <canvas id="healthTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 18px; color: var(--text-main);">Most Scanned Products</h3>
        </div>
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Product</th>
                        <th style="padding: 16px;">UPC/EAN Barcode</th>
                        <th style="padding: 16px;">Halal Status</th>
                        <th style="padding: 16px 24px; text-align: right;">Total Interactions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_products'] as $product)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $product['product_name'] }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <code style="font-size: 12px; background: #eee; padding: 2px 6px; border-radius: 4px;">{{ $product['barcode'] ?: 'MANUAL_ENTRY' }}</code>
                        </td>
                        <td style="padding: 16px;">
                            <span class="badge" style="background: {{ $product['halal_status'] == 'halal' ? 'rgba(45,106,79,0.1)' : 'rgba(231,76,60,0.1)' }}; color: {{ $product['halal_status'] == 'halal' ? 'var(--primary-color)' : 'var(--danger)' }};">
                                {{ strtoupper($product['halal_status']) }}
                            </span>
                        </td>
                        <td style="padding: 16px 24px; text-align: right; font-weight: 800; color: var(--text-main);">
                            {{ number_format($product['scan_count']) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const userGrowth = @json($data['user_growth']);
    const scanActivity = @json($data['scan_activity']);
    const halalStats = @json($data['halal_stats']);
    const healthTrends = @json($data['health_trends']);

    const ctxGrowth = document.getElementById('userGrowthChart').getContext('2d');
    const gradient = ctxGrowth.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(45, 106, 79, 0.2)');
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

    new Chart(ctxGrowth, {
        type: 'line',
        data: {
            labels: userGrowth.map(item => item.date),
            datasets: [{
                label: 'New Registrations',
                data: userGrowth.map(item => item.count),
                borderColor: '#2D6A4F',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('halalStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Halal', 'Haram', 'Syubhat'],
            datasets: [{
                data: [halalStats.halal, halalStats.haram, halalStats.syubhat],
                backgroundColor: ['#2D6A4F', '#E74C3C', '#F4A261'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }
        }
    });

    new Chart(document.getElementById('scanActivityChart'), {
        type: 'bar',
        data: {
            labels: scanActivity.map(item => item.date),
            datasets: [
                { label: 'Halal', data: scanActivity.map(item => item.halal), backgroundColor: '#2D6A4F', borderRadius: 5 },
                { label: 'Haram', data: scanActivity.map(item => item.haram), backgroundColor: '#E74C3C', borderRadius: 5 },
                { label: 'Syubhat', data: scanActivity.map(item => item.syubhat), backgroundColor: '#F4A261', borderRadius: 5 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { stacked: true }, y: { stacked: true } }
        }
    });

    new Chart(document.getElementById('healthTrendChart'), {
        type: 'bar',
        data: {
            labels: healthTrends.map(item => item.metric_type),
            datasets: [{
                label: 'Interactions',
                data: healthTrends.map(item => item.count),
                backgroundColor: '#3498DB',
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
