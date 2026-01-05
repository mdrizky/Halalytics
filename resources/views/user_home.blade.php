@extends('master')
@section('isi')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Halalytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1A5632;
            --bg-dark: #121212;
            --bg-card: #1E1E1E;
            --bg-hover: #2A2A2A;
            --text-light: #E0E0E0;
            --text-muted: #A0A0A0;
            --border-color: #333333;
            --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            --glow-effect: 0 0 10px rgba(46, 139, 87, 0.3);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
        }
        
        .page-container {
            padding: 20px;
        }
        
        .welcome-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .welcome-content {
            position: relative;
            z-index: 1;
        }
        
        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .welcome-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow), var(--glow-effect);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-icon.scan {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
        }
        
        .stat-icon.product {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }
        
        .stat-icon.report {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        
        .quick-actions {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-light);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .action-btn {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--text-light);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-btn:hover {
            background: rgba(46, 139, 87, 0.1);
            border-color: var(--primary-color);
            transform: translateY(-3px);
            color: var(--primary-light);
        }
        
        .action-btn i {
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        .action-btn:hover i {
            color: var(--primary-light);
            transform: scale(1.1);
        }
        
        .action-btn span {
            font-weight: 600;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card, .quick-actions {
            animation: fadeIn 0.5s ease-out;
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }
            
            .welcome-title {
                font-size: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="welcome-header">
            <div class="welcome-content">
                <h1 class="welcome-title">
                    <i class="fas fa-user-circle me-2"></i>
                    Selamat Datang, {{ Auth::user()->username ?? 'User' }}!
                </h1>
                <p class="welcome-subtitle">
                    Dashboard pengguna Halalytics - Verifikasi halal produk Anda dengan mudah
                </p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon scan">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="stat-value">{{ Auth::user()->scans_count ?? 0 }}</div>
                <div class="stat-label">Total Scan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon product">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-value">{{ Auth::user()->reports_count ?? 0 }}</div>
                <div class="stat-label">Total Laporan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon report">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">{{ \App\Models\ProductModel::count() }}</div>
                <div class="stat-label">Produk Tersedia</div>
            </div>
        </div>

        <div class="quick-actions">
            <h3 class="section-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h3>
            <div class="actions-grid">
                <a href="{{ url('/scan/barcode') }}" class="action-btn">
                    <i class="fas fa-search"></i>
                    <span>Scan Barcode</span>
                </a>
                <a href="{{ url('/my-scans') }}" class="action-btn">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Scan</span>
                </a>
                <a href="{{ url('/reports') }}" class="action-btn">
                    <i class="fas fa-flag"></i>
                    <span>Buat Laporan</span>
                </a>
                <a href="{{ url('/products') }}" class="action-btn">
                    <i class="fas fa-list"></i>
                    <span>Lihat Produk</span>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection
