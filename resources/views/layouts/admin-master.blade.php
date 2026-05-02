<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Halalytics Admin')</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-system.css') }}">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header" style="padding: 24px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="background: white; width: 40px; height: 40px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <i class="fas fa-hand-holding-heart" style="color: var(--primary-color); font-size: 20px;"></i>
                </div>
                <h2 style="color: white; margin: 0; font-size: 18px; letter-spacing: 1px;">HALALYTICS</h2>
                <p style="color: rgba(255,255,255,0.6); font-size: 10px; margin-top: 4px; font-weight: 600;">ADMIN PORTAL</p>
            </div>
            
            <nav class="sidebar-nav" style="flex: 1; overflow-y: auto; padding: 16px 0;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ url('/admin') }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Dashboard & Analytics
                        </a>
                    </li>
                    
                    <div class="nav-label">PRODUCT SYSTEM</div>
                    <li class="nav-item">
                        <a href="{{ url('/admin/product') }}" class="nav-link {{ request()->is('admin/product*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i> Product Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/requests') }}" class="nav-link {{ request()->is('admin/requests*') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Pending Requests
                            @if(isset($pending_requests_count) && $pending_requests_count > 0)
                                <span class="nav-badge">{{ $pending_requests_count }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/bpom') }}" class="nav-link {{ request()->is('admin/bpom*') ? 'active' : '' }}">
                            <i class="fas fa-shield-halal"></i> BPOM Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/kategori') }}" class="nav-link {{ request()->is('admin/kategori*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i> Category Management
                        </a>
                    </li>
                    
                    <div class="nav-label">HEALTH & BEAUTY</div>
                    <li class="nav-item">
                        <a href="{{ url('/admin/medicines') }}" class="nav-link {{ request()->is('admin/medicines*') ? 'active' : '' }}">
                            <i class="fas fa-pills"></i> Medicine Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/cosmetics') }}" class="nav-link {{ request()->is('admin/cosmetics*') ? 'active' : '' }}">
                            <i class="fas fa-pump-soap"></i> Cosmetic Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/ingredients') }}" class="nav-link {{ request()->is('admin/ingredients*') ? 'active' : '' }}">
                            <i class="fas fa-flask"></i> Ingredient Encyclopedia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/forbidden') }}" class="nav-link {{ request()->is('admin/forbidden*') ? 'active' : '' }}">
                            <i class="fas fa-skull-crossbones"></i> Forbidden Ingredients
                        </a>
                    </li>
                    
                    <div class="nav-label">CONTENT & PROMO</div>
                    <li class="nav-item">
                        <a href="{{ url('/admin/promo/blog') }}" class="nav-link {{ request()->is('admin/promo/blog*') ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i> Article Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/banner') }}" class="nav-link {{ request()->is('admin/banner*') ? 'active' : '' }}">
                            <i class="fas fa-images"></i> Banner & Slider
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/street-foods') }}" class="nav-link {{ request()->is('admin/street-foods*') ? 'active' : '' }}">
                            <i class="fas fa-utensils"></i> Street Food
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/halal-products') }}" class="nav-link {{ request()->is('admin/halal-products*') ? 'active' : '' }}">
                            <i class="fas fa-certificate"></i> Halal Products
                        </a>
                    </li>
                    
                    <div class="nav-label">COMMUNICATION</div>
                    <li class="nav-item">
                        <a href="{{ url('/admin/campaigns') }}" class="nav-link {{ request()->is('admin/campaigns*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i> Campaigns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/ocr') }}" class="nav-link {{ request()->is('admin/ocr*') ? 'active' : '' }}">
                            <i class="fas fa-eye"></i> OCR Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/blood-donor') }}" class="nav-link {{ request()->is('admin/blood-donor*') ? 'active' : '' }}">
                            <i class="fas fa-droplet"></i> Blood Donor
                        </a>
                    </li>
                    
                    <div class="nav-label">REPORTS & ORDERS</div>
                    <li class="nav-item">
                        <a href="{{ url('/admin/scan') }}" class="nav-link {{ request()->is('admin/scan*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i> Scan History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/reports') }}" class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> User Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/orders') }}" class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i> Order Management
                        </a>
                    </li>
                    
                    <div class="nav-label">SYSTEM</div>
                    <li class="nav-item">
                        <a href="{{ url('/admin/users') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/settings') }}" class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer" style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width: 100%; background: rgba(255,255,255,0.1); border: none; color: white; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="main-wrapper" style="flex: 1; display: flex; flex-direction: column;">
            <!-- Topbar -->
            <header class="topbar" style="height: 70px; background: white; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; padding: 0 32px;">
                <div class="search-bar" style="position: relative; width: 400px;">
                    <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" placeholder="Global search..." style="width: 100%; padding: 10px 10px 10px 44px; border-radius: 99px; border: 1px solid var(--border-color); background: var(--bg-light);">
                </div>
                
                <div class="topbar-actions" style="display: flex; align-items: center; gap: 24px;">
                    <div class="notification-bell" style="position: relative; cursor: pointer;">
                        <i class="far fa-bell" style="font-size: 20px; color: var(--text-muted);"></i>
                        <span style="position: absolute; top: -5px; right: -5px; background: var(--accent-color); color: white; font-size: 10px; font-weight: 800; padding: 2px 5px; border-radius: 10px;">3</span>
                    </div>
                    
                    <div class="user-profile" style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <div class="avatar" style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div style="font-size: 14px; font-weight: 700;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">Super Admin</div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Breadcrumbs & Content -->
            <main class="main-content">
                <nav class="breadcrumb" style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 13px;">
                    <a href="{{ url('/admin') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                    @yield('breadcrumb-items')
                </nav>
                
                @if(session('success'))
                    <div class="alert alert-success" style="background: #D4EDDA; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 24px; border-left: 5px solid #28A745;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    <style>
        .nav-label {
            padding: 24px 24px 8px;
            font-size: 10px;
            font-weight: 800;
            color: rgba(255,255,255,0.4);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }
        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }
        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            font-weight: 700;
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent-color);
        }
        .nav-badge {
            margin-left: auto;
            background: var(--accent-color);
            color: white;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 10px;
        }
        /* Custom scrollbar for sidebar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
    
    @stack('scripts')
</body>
</html>
