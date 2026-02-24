@extends('admin.master')

@section('title', 'Admin Dashboard - Halalytics')

@section('extra_css')
<style>
    .stat-card {
        border: none;
        overflow: hidden;
        position: relative;
    }
    .stat-card .icon-shape {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
    }
    .stat-card .trend-badge {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 100px;
        font-weight: 600;
    }
    .card-gradient-1 { background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), transparent); }
    .card-gradient-2 { background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent); }
    .card-gradient-3 { background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), transparent); }
    .card-gradient-4 { background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), transparent); }
    
    .chart-container {
        position: relative;
        width: 100%;
    }
</style>
@endsection

@section('isi')
<div class="container-fluid px-4">
    <!-- Welcome Header -->
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-white mb-1" style="letter-spacing: -0.02em;">Digital Cockpit</h2>
            <p class="text-muted mb-0 fw-medium">Overview of Halalytics ecosystem performance and activities.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="btn-group shadow-sm">
                <button class="btn btn-primary px-4 rounded-start-pill py-2" onclick="refreshStats()">
                    <i class="bi bi-arrow-repeat me-2"></i> Sync Data
                </button>
                <div class="dropdown d-inline">
                    <button class="btn btn-dark px-3 rounded-end-pill py-2 border-start border-white border-opacity-10" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end glass-card border-0 mt-2 shadow-2xl">
                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-file-pdf me-2"></i> Report PDF</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-file-spreadsheet me-2"></i> Export CSV</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-4 mb-5">
        <!-- Products -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card stat-card card-gradient-1 p-4 h-100">
                <div class="d-flex justify-content-between mb-4">
                    <div class="icon-shape bg-success bg-opacity-10 text-success">
                        <i class="bi bi-box-seam-fill fs-3"></i>
                    </div>
                    <div class="text-end">
                        <span class="trend-badge bg-success bg-opacity-10 text-success">
                            <i class="bi bi-arrow-up-short"></i> +14%
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-1">{{ number_format($stats['total_products']) }}</h3>
                    <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 0.05em;">Total Products</p>
                </div>
                <div class="mt-4 pt-4 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small">Verified Index</span>
                    <span class="text-success small fw-bold">82%</span>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card stat-card card-gradient-2 p-4 h-100">
                <div class="d-flex justify-content-between mb-4">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div class="text-end">
                        <span class="trend-badge bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-arrow-up-short"></i> +8.2%
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-1">{{ number_format($stats['total_users']) }}</h3>
                    <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 0.05em;">Active Members</p>
                </div>
                <div class="mt-4 pt-4 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small">New this week</span>
                    <span class="text-primary small fw-bold">+124</span>
                </div>
            </div>
        </div>

        <!-- Scans -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card stat-card card-gradient-3 p-4 h-100">
                <div class="d-flex justify-content-between mb-4">
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-qr-code-scan fs-3"></i>
                    </div>
                    <div class="text-end">
                        <span class="trend-badge bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-arrow-down-short"></i> -2%
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-1">{{ number_format($stats['total_scans']) }}</h3>
                    <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 0.05em;">Scans Completed</p>
                </div>
                <div class="mt-4 pt-4 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small">Accuracy Rate</span>
                    <span class="text-warning small fw-bold">99.4%</span>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-xl-3 col-md-6">
            <div class="glass-card stat-card card-gradient-4 p-4 h-100">
                <div class="d-flex justify-content-between mb-4">
                    <div class="icon-shape bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-octagon-fill fs-3"></i>
                    </div>
                    <div class="text-end">
                        <span class="trend-badge bg-danger bg-opacity-10 text-danger">
                            Action Required
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-1">{{ $stats['total_reports'] }}</h3>
                    <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 0.05em;">Active Reports</p>
                </div>
                <div class="mt-4 pt-4 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small">SLA Response</span>
                    <span class="text-danger small fw-bold">2.4h</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Distribution Chart -->
        <div class="col-lg-5">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-white mb-0">Halal Distribution</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm text-white-50 p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu glass-card border-0 shadow-lg">
                            <li><a class="dropdown-item small" href="#">Detailed View</a></li>
                        </ul>
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="halalChart"></canvas>
                </div>
                <div class="mt-4 d-grid gap-2">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-white bg-opacity-5">
                        <span class="small text-muted"><i class="bi bi-circle-fill text-success me-2" style="font-size: 0.5rem;"></i> Verified Halal</span>
                        <span class="small fw-bold text-white">{{ $stats['halal_products'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-white bg-opacity-5">
                        <span class="small text-muted"><i class="bi bi-circle-fill text-warning me-2" style="font-size: 0.5rem;"></i> Syubhat (Doubtful)</span>
                        <span class="small fw-bold text-white">{{ $stats['syubhat_products'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-white bg-opacity-5">
                        <span class="small text-muted"><i class="bi bi-circle-fill text-danger me-2" style="font-size: 0.5rem;"></i> Haram (Prohibited)</span>
                        <span class="small fw-bold text-white">{{ $stats['non_halal_products'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends Chart -->
        <div class="col-lg-7">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-white mb-0">Scanning Trends</h5>
                        <small class="text-muted fw-medium">Real-time scan frequency volume</small>
                    </div>
                    <ul class="nav nav-pills bg-black bg-opacity-20 p-1 rounded-pill" style="font-size: 0.75rem;">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill px-3 py-1" data-bs-toggle="pill">Monthly</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill px-3 py-1 text-white-50" data-bs-toggle="pill">Weekly</button>
                        </li>
                    </ul>
                </div>
                <div class="chart-container" style="height: 380px;">
                    <canvas id="scanTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Table -->
    <div class="glass-card overflow-hidden">
        <div class="p-4 bg-white bg-opacity-5 border-bottom border-white border-opacity-5">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-white mb-0">Global Scan Stream</h5>
                    <small class="text-muted fw-medium">Monitoring latest verification activities across the network</small>
                </div>
                <a href="{{ route('admin.scan.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    View Real-time Map
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-glass align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Authority/User</th>
                        <th>Product Entity</th>
                        <th>UPC/Barcode</th>
                        <th>Risk Assessment</th>
                        <th>Time Elapsed</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_scans as $scan)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-white small">{{ $scan->username }}</div>
                                    <div class="text-muted small" style="font-size: 0.65rem;">Direct API App</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium text-white-50 small">{{ Str::limit($scan->nama_produk, 30) }}</div>
                        </td>
                        <td><span class="badge bg-black bg-opacity-30 text-success font-monospace">{{ $scan->barcode }}</span></td>
                        <td>
                             @if($scan->status_halal == 'halal')
                                <div class="d-flex align-items-center text-success small fw-bold">
                                    <i class="bi bi-shield-check me-2"></i> LOW RISK
                                </div>
                            @elseif($scan->status_halal == 'syubhat')
                                <div class="d-flex align-items-center text-warning small fw-bold">
                                    <i class="bi bi-shield-exclamation me-2"></i> MODERATE
                                </div>
                            @else
                                <div class="d-flex align-items-center text-danger small fw-bold">
                                    <i class="bi bi-shield-slash me-2"></i> HIGH RISK
                                </div>
                            @endif
                        </td>
                        <td class="text-muted small fw-medium">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->diffForHumans() }}</td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-dark btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 12;

    // Halal Distribution Chart
    const halalCtx = document.getElementById('halalChart').getContext('2d');
    new Chart(halalCtx, {
        type: 'doughnut',
        data: {
            labels: ['Halal', 'Syubhat', 'Haram', 'Pending'],
            datasets: [{
                data: [{{ $stats['halal_products'] }}, {{ $stats['syubhat_products'] }}, {{ $stats['non_halal_products'] }}, {{ $stats['unverified_products'] }}],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#334155'],
                hoverBackgroundColor: ['#059669', '#D97706', '#DC2626', '#475569'],
                borderWidth: 0,
                spacing: 8,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Scan Trends Chart
    const trendsCtx = document.getElementById('scanTrendsChart').getContext('2d');
    const chartGradient = trendsCtx.createLinearGradient(0, 0, 0, 400);
    chartGradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    chartGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: @json($monthly_scans->pluck('month')),
            datasets: [{
                label: 'Global Volume',
                data: @json($monthly_scans->pluck('count')),
                borderColor: '#10B981',
                borderWidth: 4,
                pointBackgroundColor: '#10B981',
                pointBorderColor: 'rgba(255,255,255,0.1)',
                pointBorderWidth: 5,
                pointRadius: 6,
                pointHoverRadius: 8,
                tension: 0.45,
                fill: true,
                backgroundColor: chartGradient
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.03)', drawBorder: false },
                    ticks: { padding: 10 }
                },
                x: {
                    grid: { display: false },
                    ticks: { padding: 10 }
                }
            }
        }
    });

    function refreshStats() {
        location.reload();
    }
</script>
@endsection
