@extends('admin.layouts.admin_layout')

@section('title', 'Scan Detail - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Activity</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Scan Detail</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Scan Record Details</h3>
            <a href="{{ route('admin.scan.index') }}" class="text-sm font-medium text-primary hover:underline flex items-center">
                <span class="material-icons-round text-sm mr-1">arrow_back</span>
                Back to History
            </a>
        </div>
    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1A5632;
            --accent-color: #4CAF50;
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
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            background-color: var(--bg-card);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow), var(--glow-effect);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.5rem;
            border: none;
            border-radius: 15px 15px 0 0 !important;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .card-header:hover::before {
            left: 100%;
        }
        
        .card-header h5 {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .badge-halal {
            background: linear-gradient(135deg, #38b000, #2a8500);
            color: white;
        }
        
        .badge-tidak-halal {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
        }
        
        .badge-sehat {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
        }
        
        .badge-tidak-sehat {
            background: linear-gradient(135deg, #f8961e, #e07c0c);
            color: white;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }
        
        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            position: relative;
            overflow: hidden;
            padding: 0.6rem 1.2rem;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            box-shadow: 0 0 10px rgba(46, 139, 87, 0.5);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
            border: none;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #f25c68, #e63946);
            box-shadow: 0 0 10px rgba(230, 57, 70, 0.5);
        }
        
        /* Detail Styles */
        .detail-grid {
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .detail-value {
            font-weight: 500;
            text-align: left;
            width: 100%;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .expired-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .barcode-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--primary-light);
            font-size: 0.9rem;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.2);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-barcode"></i>
                    Detail Scan Produk
                </h5>
            </div>
            
            <div class="detail-grid">
                <!-- User -->
                <div class="detail-item">
                    <div class="detail-label">User</div>
                    <div class="detail-value">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ substr($scan->user->username ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $scan->user->username ?? 'Unknown' }}</div>
                                <div class="small text-muted">{{ $scan->user->email ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Produk -->
                <div class="detail-item">
                    <div class="detail-label">Produk</div>
                    <div class="detail-value">
                        <div class="fw-semibold">{{ $scan->nama_produk }}</div>
                        <div class="small text-muted">Scan pada: {{ \Carbon\Carbon::parse($scan->created_at)->format('d M Y H:i') }}</div>
                    </div>
                </div>
                
                <!-- Barcode -->
                <div class="detail-item">
                    <div class="detail-label">Barcode</div>
                    <div class="detail-value barcode-cell">{{ $scan->barcode ?? '-' }}</div>
                </div>
                
                <!-- Status Halal -->
                <div class="detail-item">
                    <div class="detail-label">Status Halal</div>
                    <div class="detail-value">
                        @if(strtolower($scan->status_halal) == 'halal')
                            <span class="badge badge-halal">
                                <i class="fas fa-check-circle"></i> Halal
                            </span>
                        @else
                            <span class="badge badge-tidak-halal">
                                <i class="fas fa-times-circle"></i> Tidak Halal
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Status Kesehatan -->
                <div class="detail-item">
                    <div class="detail-label">Status Kesehatan</div>
                    <div class="detail-value">
                        @if(strtolower($scan->status_kesehatan) == 'sehat')
                            <span class="badge badge-sehat">
                                <i class="fas fa-heart"></i> Sehat
                            </span>
                        @else
                            <span class="badge badge-tidak-sehat">
                                <i class="fas fa-exclamation-triangle"></i> Tidak Sehat
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Tanggal Expired -->
                <div class="detail-item">
                    <div class="detail-label">Tanggal Expired</div>
                    <div class="detail-value">
                        @if($scan->tanggal_expired)
                            @php
                                $isExpired = \Carbon\Carbon::parse($scan->tanggal_expired)->isPast();
                            @endphp
                            <div class="fw-semibold {{ $isExpired ? 'text-danger' : 'text-success' }}">
                                {{ \Carbon\Carbon::parse($scan->tanggal_expired)->format('d M Y') }}
                            </div>
                            <div class="small {{ $isExpired ? 'text-danger' : 'text-muted' }}">
                                @if($isExpired)
                                    <span class="expired-badge">Kadaluarsa</span>
                                @else
                                    {{ \Carbon\Carbon::parse($scan->tanggal_expired)->diffForHumans() }}
                                @endif
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                
                <!-- ID Scan -->
                <div class="detail-item">
                    <div class="detail-label">ID Scan</div>
                    <div class="detail-value">{{ $scan->id_scan }}</div>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.scan.index') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-all text-sm font-medium">
                        Back
                    </a>
                    <a href="{{ route('admin.scan.edit', $scan->id_scan) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all text-sm font-bold flex items-center space-x-1">
                        <span class="material-icons-round text-sm">edit</span>
                        <span>Edit</span>
                    </a>
                </div>
                <form action="{{ route('admin.scan.destroy', $scan->id_scan) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this scan record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all text-sm font-medium flex items-center space-x-1">
                        <span class="material-icons-round text-sm">delete</span>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection