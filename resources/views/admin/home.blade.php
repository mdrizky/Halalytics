@extends('master')

@section('title', 'Dashboard')

@section('isi')
<div class="dashboard-aurora">
    <div class="aurora-blobs">
        <span class="blob blob-1"></span>
        <span class="blob blob-2"></span>
        <span class="blob blob-3"></span>
    </div>

    <div class="container-fluid p-0">
        <div class="text-center py-2 small text-muted">
            Ini halaman Dashboard
        </div>
        <div id="dashboard-data"
             class="data-props"
             data-trend-labels='@json($labels30Hari)'
             data-trend-data='@json($data30Hari)'
             data-produk-halal="{{ $produkHalal }}"
             data-produk-diragukan="{{ $produkDiragukan }}"
             data-produk-haram="{{ $produkHaram }}"
             data-total-scan="{{ $totalScan }}"
             data-scan-months='@json($scanChartData)'>
        </div>
        {{-- Header / Hero --}}
        <div class="dashboard-header glass-panel">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <p class="eyebrow">Realtime Insight</p>
                    <h1 class="hero-title">Halal Verification Command Center</h1>
                    <p class="hero-subtitle">
                        Pantau ekosistem halal dengan statistik menyala, aksi cepat, dan insight real-time.
                    </p>

                    <div class="chip-row">
                        <div class="chip">
                            <span>Scan Hari Ini</span>
                            <strong>{{ number_format($scanToday) }}</strong>
                        </div>
                        <div class="chip">
                            <span>Total Produk</span>
                            <strong>{{ number_format($totalProduk) }}</strong>
                        </div>
                        <div class="chip">
                            <span>Laporan Pending</span>
                            <strong>{{ number_format($laporanMasuk) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-bubble">
                        <div class="bubble-label">Total Scan</div>
                        <div class="bubble-value" id="liveScanCount">{{ number_format($totalScan) }}</div>
                        <div class="bubble-meta">
                            <span class="meta-pill success">Realtime</span>
                            <span class="meta-pill dark">{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
                        </div>
                        <div class="bubble-grid">
                            <div class="bubble-mini">
                                <small>User</small>
                                <strong>{{ number_format($totalUsers) }}</strong>
                            </div>
                            <div class="bubble-mini">
                                <small>Aktivitas</small>
                                <strong>{{ number_format($activities->count()) }}</strong>
                            </div>
                            <div class="bubble-mini">
                                <small>Produk Halal</small>
                                <strong>{{ number_format($produkHalal) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Besar --}}
        <div class="container-fluid py-4">
            <div class="row g-4">
                @php
                    $yesterdayScans = App\Models\ScanModel::whereDate('tanggal_scan', Carbon\Carbon::yesterday())->count();
                    $trendPercentage = $yesterdayScans > 0 ? (($scanToday - $yesterdayScans) / $yesterdayScans * 100) : 100;
                    $halalPercentage = $totalProduk > 0 ? ($produkHalal / $totalProduk * 100) : 0;
                    $diragukanPercentage = $totalProduk > 0 ? ($produkDiragukan / $totalProduk * 100) : 0;
                    $tidakHalalPercentage = $totalProduk > 0 ? ($produkHaram / $totalProduk * 100) : 0;
                @endphp

                {{-- Scan Hari Ini --}}
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card neon">
                        <div class="stat-top">
                            <div class="icon-pill primary">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <span class="pill-outline">Live</span>
                        </div>
                        <div class="stat-number">{{ number_format($scanToday) }}</div>
                        <div class="stat-label">Scan Hari Ini</div>
                        <div class="stat-trend">
                            @if($trendPercentage > 0)
                                <span class="trend-up"><i class="fas fa-arrow-up"></i> {{ number_format(abs($trendPercentage), 1) }}%</span>
                            @elseif($trendPercentage < 0)
                                <span class="trend-down"><i class="fas fa-arrow-down"></i> {{ number_format(abs($trendPercentage), 1) }}%</span>
                            @else
                                <span class="trend-warning"><i class="fas fa-minus"></i> 0%</span>
                            @endif
                            <span class="trend-text">vs kemarin</span>
                        </div>
                    </div>
                </div>

                {{-- Produk Halal --}}
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card success">
                        <div class="stat-top">
                            <div class="icon-pill success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="pill-outline">Dominan</span>
                        </div>
                        <div class="stat-number">{{ number_format($produkHalal) }}</div>
                        <div class="stat-label">Produk Halal</div>
                        <div class="stat-trend">
                            <span class="trend-up"><i class="fas fa-chart-line"></i> {{ number_format($halalPercentage, 1) }}%</span>
                            <span class="trend-text">dari total</span>
                        </div>
                    </div>
                </div>

                {{-- Perlu Verifikasi --}}
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card warning">
                        <div class="stat-top">
                            <div class="icon-pill warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <span class="pill-outline">Tinjau</span>
                        </div>
                        <div class="stat-number">{{ number_format($produkDiragukan) }}</div>
                        <div class="stat-label">Perlu Verifikasi</div>
                        <div class="stat-trend">
                            <span class="trend-warning"><i class="fas fa-clock"></i> {{ number_format($diragukanPercentage, 1) }}%</span>
                            <span class="trend-text">dari total</span>
                        </div>
                    </div>
                </div>

                {{-- Tidak Halal --}}
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card danger">
                        <div class="stat-top">
                            <div class="icon-pill danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span class="pill-outline">Critical</span>
                        </div>
                        <div class="stat-number">{{ number_format($produkHaram) }}</div>
                        <div class="stat-label">Tidak Halal</div>
                        <div class="stat-trend">
                            <span class="trend-down"><i class="fas fa-arrow-down"></i> {{ number_format($tidakHalalPercentage, 1) }}%</span>
                            <span class="trend-text">dari total</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="container-fluid">
            <div class="row g-4">
                {{-- Grafik Trend Besar --}}
                <div class="col-lg-8">
                    <div class="panel-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="mini-dot primary"></div>
                                <h3 class="card-title mb-0">Trend Scan 30 Hari</h3>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-sm btn-outline-glow" onclick="filterChart(event, 'week')">Minggu Ini</button>
                                <button class="btn btn-sm btn-outline-glow" onclick="filterChart(event, 'month')">Bulan Ini</button>
                                <button class="btn btn-sm btn-primary-glow active" onclick="filterChart(event, '30days')">30 Hari</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-large">
                                <canvas id="bigTrendChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Database Overview --}}
                <div class="col-lg-4">
                    <div class="panel-card stacked">
                        <div class="card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="mini-dot purple"></div>
                                <h3 class="card-title mb-0">Database Overview</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="database-list">
                                <div class="db-item">
                                    <div class="db-icon"><i class="fas fa-users"></i></div>
                                    <div class="db-content">
                                        <span class="db-name">Data User</span>
                                        <span class="db-count">{{ number_format($totalUsers) }}</span>
                                    </div>
                                </div>
                                <div class="db-item">
                                    <div class="db-icon"><i class="fas fa-box"></i></div>
                                    <div class="db-content">
                                        <span class="db-name">Data Produk</span>
                                        <span class="db-count">{{ number_format($totalProduk) }}</span>
                                    </div>
                                </div>
                                <div class="db-item">
                                    <div class="db-icon"><i class="fas fa-search"></i></div>
                                    <div class="db-content">
                                        <span class="db-name">Data Scan</span>
                                        <span class="db-count">{{ number_format($totalScan) }}</span>
                                    </div>
                                </div>
                                <div class="db-item">
                                    <div class="db-icon"><i class="fas fa-file-alt"></i></div>
                                    <div class="db-content">
                                        <span class="db-name">Laporan Masuk</span>
                                        <span class="db-count">{{ number_format($laporanMasuk) }}</span>
                                    </div>
                                </div>
                                <div class="db-item">
                                    <div class="db-icon"><i class="fas fa-chart-bar"></i></div>
                                    <div class="db-content">
                                        <span class="db-name">Aktivitas</span>
                                        <span class="db-count">{{ number_format($activities->count()) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions & Status --}}
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="panel-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="mini-dot yellow"></div>
                                <h3 class="card-title mb-0">Quick Actions</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="action-grid">
                                <a href="{{ route('scan.index') }}" class="action-btn action-scan">
                                    <i class="fas fa-qrcode"></i>
                                    <span>Scan QR Code</span>
                                </a>
                                <a href="{{ route('admin.product_tambah') }}" class="action-btn action-add">
                                    <i class="fas fa-plus"></i>
                                    <span>Tambah Produk</span>
                                </a>
                                <a href="{{ route('admin_report') }}" class="action-btn action-report">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Buat Laporan</span>
                                </a>
                                <a href="{{ route('admin.product_tambah') }}" class="action-btn action-analytics">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Lihat Analytics</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="mini-dot teal"></div>
                                <h3 class="card-title mb-0">Status Produk</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="status-chart">
                                <canvas id="bigStatusChart" height="200"></canvas>
                            </div>
                            <div class="status-legends">
                                <div class="legend-item">
                                    <span class="legend-color halal"></span>
                                    <span class="legend-text">Halal</span>
                                    <span class="legend-value">{{ number_format($halalPercentage, 1) }}%</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color diragukan"></span>
                                    <span class="legend-text">Diragukan</span>
                                    <span class="legend-value">{{ number_format($diragukanPercentage, 1) }}%</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color tidak-halal"></span>
                                    <span class="legend-text">Tidak Halal</span>
                                    <span class="legend-value">{{ number_format($tidakHalalPercentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="row g-4">
                <div class="col-12">
                    <div class="panel-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="mini-dot pink"></div>
                                <h3 class="card-title mb-0">Aktivitas Terbaru</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                @forelse($activities as $activity)
                                    <div class="activity-item">
                                        <div class="activity-timeline"></div>
                                        <div class="activity-icon">
                                            <i class="fas fa-{{ $activity->type === 'scan' ? 'qrcode' : ($activity->type === 'login' ? 'sign-in-alt' : 'user') }}"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-text">
                                                <strong>{{ $activity->user->name ?? 'User' }}</strong> 
                                                {{ $activity->description }}
                                            </div>
                                            <div class="activity-time">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada aktivitas terbaru</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari controller
        const dataNode = document.getElementById('dashboard-data');
        const trendLabels = JSON.parse(dataNode.dataset.trendLabels || '[]');
        const trendData = JSON.parse(dataNode.dataset.trendData || '[]');
        const scanMonths = JSON.parse(dataNode.dataset.scanMonths || '[]');
        const statusData = {
            halal: Number(dataNode.dataset.produkHalal || 0),
            diragukan: Number(dataNode.dataset.produkDiragukan || 0),
            tidakHalal: Number(dataNode.dataset.produkHaram || 0)
        };
        const totalScan = Number(dataNode.dataset.totalScan || 0);

        const trendCtx = document.getElementById('bigTrendChart').getContext('2d');
        const gradient = trendCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(94, 234, 212, 0.35)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        // Keep originals for filters
        const originalTrendLabels = [...trendLabels];
        const originalTrendData = [...trendData];
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Grafik Trend Besar
        var trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Jumlah Scan',
                    data: trendData,
                    backgroundColor: gradient,
                    borderColor: 'rgba(94, 234, 212, 1)',
                    borderWidth: 4,
                    pointBackgroundColor: '#38bdf8',
                    pointBorderColor: '#0b1221',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 9,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 16, weight: 'bold' },
                        bodyFont: { size: 14 },
                        padding: 14,
                        borderColor: 'rgba(94, 234, 212, 0.4)',
                        borderWidth: 1
                    }
                },
                elements: {
                    line: { borderCapStyle: 'round', borderJoinStyle: 'round' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: 'rgba(148, 163, 184, 0.12)',
                            borderDash: [6, 6]
                        },
                        ticks: { 
                            color: '#e2e8f0',
                            font: { size: 12 }
                        }
                    },
                    x: {
                        grid: { 
                            color: 'rgba(148, 163, 184, 0.08)',
                            borderDash: [4, 4]
                        },
                        ticks: { 
                            color: '#e2e8f0',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });

        // Chart Status Produk Besar
        var statusChart = new Chart(document.getElementById('bigStatusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Halal', 'Diragukan', 'Tidak Halal'],
                datasets: [{
                    data: [statusData.halal, statusData.diragukan, statusData.tidakHalal],
                    backgroundColor: ['#22c55e', '#fbbf24', '#f87171'],
                    borderWidth: 2,
                    borderColor: '#0b1221',
                    hoverOffset: 16
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        bodyFont: { size: 14 },
                        padding: 12,
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1
                    }
                }
            }
        });

        // Real-time counter animation
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 20);
        }

        // Animate counters on load
        animateCounter(document.getElementById('liveScanCount'), totalScan);

        // Filter chart function
        window.filterChart = function(event, type) {
            // Remove active class from all buttons
            document.querySelectorAll('.card-actions .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            event.target.classList.add('active');
            
            let filteredLabels = originalTrendLabels;
            let filteredData = originalTrendData;

            if (type === 'week') {
                filteredLabels = originalTrendLabels.slice(-7);
                filteredData = originalTrendData.slice(-7);
            } else if (type === 'month') {
                filteredLabels = monthNames;
                filteredData = monthNames.map((_, idx) => Number(scanMonths[idx] || 0));
            }

            trendChart.data.labels = filteredLabels;
            trendChart.data.datasets[0].data = filteredData;
            trendChart.update();
        };
    });
</script>

<style>
    .dashboard-aurora {
        position: relative;
        background: radial-gradient(circle at 20% 20%, rgba(79, 70, 229, 0.12), transparent 25%),
                    radial-gradient(circle at 80% 0%, rgba(16, 185, 129, 0.12), transparent 25%),
                    linear-gradient(135deg, #0b1221 0%, #0f172a 100%);
        min-height: 100vh;
        color: #e2e8f0;
        padding: 1.5rem;
    }

    .data-props {
        display: none;
    }

    .aurora-blobs .blob {
        position: absolute;
        border-radius: 9999px;
        filter: blur(90px);
        opacity: 0.4;
        z-index: 0;
    }

    .blob-1 { width: 320px; height: 320px; background: #38bdf8; top: 10%; left: 5%; }
    .blob-2 { width: 260px; height: 260px; background: #a855f7; top: 5%; right: 15%; }
    .blob-3 { width: 300px; height: 300px; background: #22c55e; bottom: 10%; left: 30%; }

    .glass-panel {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        overflow: hidden;
    }

    .dashboard-header {
        margin-bottom: 1.5rem;
        backdrop-filter: blur(16px);
    }

    .eyebrow {
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .hero-title {
        font-size: 2.6rem;
        font-weight: 800;
        color: #f8fafc;
        margin-bottom: 0.5rem;
        text-shadow: 0 6px 30px rgba(56, 189, 248, 0.25);
    }

    .hero-subtitle {
        color: #cbd5e1;
        font-size: 1.05rem;
        max-width: 760px;
        margin-bottom: 1.25rem;
    }

    .chip-row {
        display: flex;
        gap: 0.85rem;
        flex-wrap: wrap;
    }

    .chip {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 999px;
        padding: 0.55rem 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
    }

    .chip span {
        color: #cbd5e1;
        font-size: 0.9rem;
    }

    .chip strong {
        color: #f8fafc;
        font-size: 1rem;
    }

    .hero-bubble {
        background: radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.12), transparent 60%),
                    rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 1.6rem;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.35);
        position: relative;
    }

    .hero-bubble::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 20px;
        border: 1px solid rgba(59, 130, 246, 0.25);
        pointer-events: none;
    }

    .bubble-label {
        color: #94a3b8;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .bubble-value {
        font-size: 3rem;
        font-weight: 800;
        color: #38bdf8;
        text-shadow: 0 10px 30px rgba(56, 189, 248, 0.45);
    }

    .bubble-meta {
        display: flex;
        gap: 0.5rem;
        margin: 0.75rem 0 1rem;
    }

    .meta-pill {
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .meta-pill.success {
        background: rgba(16, 185, 129, 0.15);
        color: #4ade80;
    }

    .meta-pill.dark {
        background: rgba(15, 23, 42, 0.8);
        color: #cbd5e1;
    }

    .bubble-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .bubble-mini {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 0.75rem 0.85rem;
    }

    .bubble-mini small {
        color: #94a3b8;
        display: block;
        font-size: 0.8rem;
    }

    .bubble-mini strong {
        color: #e2e8f0;
        font-size: 1.1rem;
    }

    /* Stat Cards */
    .stat-card {
        position: relative;
        z-index: 1;
        background: linear-gradient(145deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 18px;
        padding: 1.5rem;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        backdrop-filter: blur(8px);
        height: 100%;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.08), transparent 45%);
        opacity: 0.6;
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 22px 50px rgba(56, 189, 248, 0.25);
        border-color: rgba(56, 189, 248, 0.25);
    }

    .stat-card.neon { border-left: 4px solid #38bdf8; }
    .stat-card.success { border-left: 4px solid #22c55e; }
    .stat-card.warning { border-left: 4px solid #fbbf24; }
    .stat-card.danger { border-left: 4px solid #f87171; }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .icon-pill {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        color: #0b1221;
        font-size: 1.3rem;
        font-weight: bold;
    }

    .icon-pill.primary { background: linear-gradient(135deg, #38bdf8, #6366f1); }
    .icon-pill.success { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .icon-pill.warning { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
    .icon-pill.danger { background: linear-gradient(135deg, #f87171, #ef4444); }

    .pill-outline {
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        padding: 0.35rem 0.8rem;
        font-size: 0.85rem;
        color: #cbd5e1;
    }

    .stat-number {
        font-size: 2.6rem;
        font-weight: 800;
        margin: 0.1rem 0;
        color: #e2e8f0;
    }

    .stat-label {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: #94a3b8;
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .trend-up { color: #22c55e; font-weight: 700; }
    .trend-warning { color: #fbbf24; font-weight: 700; }
    .trend-down { color: #f87171; font-weight: 700; }
    .trend-text { color: #94a3b8; font-size: 0.9rem; }

    /* Panels */
    .panel-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(10px);
    }

    .panel-card.stacked .card-body { padding: 1.3rem 1.5rem 1.8rem; }

    .card-header {
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.08), rgba(99, 102, 241, 0.05));
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding: 1.1rem 1.4rem;
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #e2e8f0;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem 1.4rem 1.6rem;
    }

    .chart-container-large {
        height: 320px;
        position: relative;
    }

    .card-actions .btn {
        margin-left: 0.35rem;
    }

    .btn-primary-glow {
        background: linear-gradient(135deg, #38bdf8, #6366f1);
        border: none;
        color: white;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.45rem 1.2rem;
        box-shadow: 0 12px 30px rgba(99, 102, 241, 0.35);
    }

    .btn-primary-glow.active {
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.5);
    }

    .btn-outline-glow {
        border: 1px solid rgba(99, 102, 241, 0.6);
        color: #cbd5e1;
        background: rgba(99, 102, 241, 0.08);
        border-radius: 10px;
        padding: 0.45rem 1.2rem;
        font-weight: 700;
    }

    .btn-outline-glow.active {
        background: rgba(99, 102, 241, 0.18);
        color: #e2e8f0;
    }

    .mini-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 0 12px currentColor;
    }

    .mini-dot.primary { background: #38bdf8; color: #38bdf8; }
    .mini-dot.purple { background: #a855f7; color: #a855f7; }
    .mini-dot.yellow { background: #fbbf24; color: #fbbf24; }
    .mini-dot.teal { background: #14b8a6; color: #14b8a6; }
    .mini-dot.pink { background: #fb7185; color: #fb7185; }

    /* Database List */
    .database-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .db-item {
        display: flex;
        align-items: center;
        padding: 0.95rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .db-item:hover {
        transform: translateY(-3px);
        border-color: rgba(56, 189, 248, 0.35);
    }

    .db-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        margin-right: 0.85rem;
        background: rgba(99, 102, 241, 0.12);
        color: #cbd5e1;
        font-size: 1.2rem;
    }

    .db-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .db-name {
        color: #cbd5e1;
        font-weight: 600;
    }

    .db-count {
        color: #38bdf8;
        font-weight: 800;
        font-size: 1.1rem;
    }

    /* Action Grid */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.4rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        text-decoration: none;
        color: #e2e8f0;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .action-btn i {
        font-size: 1.8rem;
        margin-bottom: 0.4rem;
    }

    .action-btn span {
        font-weight: 700;
    }

    .action-btn:hover {
        transform: translateY(-4px);
        text-decoration: none;
        color: #e2e8f0;
        box-shadow: 0 18px 40px rgba(56, 189, 248, 0.25);
        border-color: rgba(56, 189, 248, 0.35);
    }

    .action-scan { background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(14, 165, 233, 0.12)); }
    .action-add { background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(16, 185, 129, 0.12)); }
    .action-report { background: linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(245, 158, 11, 0.12)); }
    .action-analytics { background: linear-gradient(135deg, rgba(129, 140, 248, 0.18), rgba(99, 102, 241, 0.12)); }

    /* Status Legends */
    .status-legends {
        display: flex;
        justify-content: space-around;
        margin-top: 1.5rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .legend-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.6rem 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        min-width: 110px;
    }

    .legend-color {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        margin-bottom: 0.4rem;
    }

    .legend-color.halal { background: #22c55e; }
    .legend-color.diragukan { background: #fbbf24; }
    .legend-color.tidak-halal { background: #f87171; }

    .legend-text {
        font-size: 0.9rem;
        color: #cbd5e1;
        margin-bottom: 0.15rem;
    }

    .legend-value {
        font-size: 1rem;
        font-weight: 800;
        color: #e2e8f0;
    }

    /* Activity List */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .activity-item {
        position: relative;
        display: flex;
        align-items: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: border-color 0.2s ease, transform 0.2s ease;
        overflow: hidden;
    }

    .activity-item:hover {
        border-color: rgba(56, 189, 248, 0.25);
        transform: translateY(-3px);
    }

    .activity-timeline {
        position: absolute;
        left: 26px;
        top: 50%;
        transform: translateY(-50%);
        width: 2px;
        height: 70%;
        background: linear-gradient(180deg, rgba(56, 189, 248, 0.35), rgba(99, 102, 241, 0.35));
        opacity: 0.7;
    }

    .activity-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(99, 102, 241, 0.15));
        color: #cbd5e1;
        font-size: 1.3rem;
        margin-right: 1rem;
        z-index: 1;
    }

    .activity-content {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        z-index: 1;
    }

    .activity-text {
        color: #e2e8f0;
        font-weight: 600;
    }

    .activity-time {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .dashboard-aurora { padding: 1rem; }
        .hero-title { font-size: 2.2rem; }
    }

    @media (max-width: 768px) {
        .glass-panel { padding: 1.5rem; }
        .hero-title { font-size: 1.9rem; }
        .chip-row { gap: 0.6rem; }
        .bubble-grid { grid-template-columns: repeat(2, 1fr); }
        .action-grid { grid-template-columns: 1fr; }
        .card-actions { margin-top: 0.8rem; }
        .activity-content {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection