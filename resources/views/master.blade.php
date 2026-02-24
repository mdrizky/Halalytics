<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Halalytics - Admin Dashboard</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
    <!-- Menambahkan Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/pg-calendar/css/pignose.calendar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme-system.css') }}" rel="stylesheet">
    <style>
        /* Light Theme Variables */
        :root {
            --primary-color: #10B981;
            --primary-dark: #047857;
            --primary-light: #064E3B;
            --secondary-color: #8B5CF6;
            --accent-color: #F59E0B;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --gold-color: #FBBF24;
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        /* Dark Theme (Default) */
        [data-theme="dark"] {
            --bg-color: #0F172A;
            --card-bg: #1E293B;
            --border-color: #334155;
            --text-primary: #F1F5F9;
            --text-secondary: #94A3B8;
            --gradient-bg: linear-gradient(135deg, #1E293B, #0F172A);
            --hover-bg: rgba(255, 255, 255, 0.05);
            --shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Light Theme */
        [data-theme="light"] {
            --bg-color: #F8F9FA;
            --card-bg: #FFFFFF;
            --border-color: #E5E7EB;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --gradient-bg: linear-gradient(135deg, #FFFFFF, #F3F4F6);
            --hover-bg: rgba(0, 0, 0, 0.05);
            --shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }
        
        /* Preloader styling */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .sk-child {
            background-color: var(--primary-color);
        }
        
        .sk-three-bounce {
            display: flex;
            gap: 10px;
        }
        
        .sk-child {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            animation: sk-three-bounce 1.4s ease-in-out 0s infinite both;
        }
        
        .sk-bounce1 { animation-delay: -0.32s; }
        .sk-bounce2 { animation-delay: -0.16s; }
        
        @keyframes sk-three-bounce {
            0%, 80%, 100% {
                transform: scale(0);
            } 40% {
                transform: scale(1.0);
            }
        }
        
        /* Theme Switcher Button */
        .theme-switcher {
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-primary);
            font-size: 1.3rem;
            position: relative;
            overflow: hidden;
        }
        
        .theme-switcher:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }
        
        .theme-switcher i {
            transition: transform 0.3s ease;
        }
        
        .theme-switcher:hover i {
            transform: rotate(180deg);
        }
        
        /* Nav header styling */
        .nav-header {
            background: var(--gradient-bg) !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .brand-title {
            color: var(--primary-color) !important;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
            font-size: 1.8rem;
            letter-spacing: 0.5px;
        }
        
        /* Header styling */
        .header {
            background: var(--gradient-bg);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid var(--border-color);
            padding: 0.5rem 0;
            transition: all 0.3s ease;
        }
        
        /* Sidebar styling */
        .quixnav {
            background: var(--gradient-bg);
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            border-right: 1px solid var(--border-color);
            height: calc(100vh - 80px);
            position: sticky;
            top: 80px;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .metismenu .nav-label {
            color: var(--text-secondary);
            font-weight: 600;
            padding: 20px 15px 10px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 10px;
        }
        
        .metismenu a {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 12px 15px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            border-radius: 8px;
            margin: 2px 10px;
            display: flex;
            align-items: center;
        }
        
        .metismenu a:hover, 
        .metismenu a:focus, 
        .metismenu a.mm-active {
            color: var(--primary-color);
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.1), transparent);
            border-left: 3px solid var(--primary-color);
            transform: translateX(5px);
            text-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }
        
        /* Logout button styling */
        .logout-item {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding-top: 10px;
        }
        
        .logout-item a {
            color: var(--danger-color) !important;
            background: rgba(239, 68, 68, 0.1) !important;
        }
        
        .logout-item a:hover {
            color: white !important;
            background: var(--danger-color) !important;
            border-left: 3px solid var(--danger-color) !important;
        }
        
        .metismenu .menu-icon {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-right: 10px;
            width: 24px;
            text-align: center;
        }
        
        .logout-item .menu-icon {
            color: var(--danger-color) !important;
        }
        
        .logout-item a:hover .menu-icon {
            color: white !important;
        }
        
        /* Content body styling */
        .content-body {
            background-color: var(--bg-color);
            padding: 20px;
            min-height: calc(100vh - 160px);
            transition: background-color 0.3s ease;
        }
        
        .main-container {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        /* Footer styling */
        .footer {
            background: var(--gradient-bg);
            padding: 15px;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.1);
            border-top: 1px solid var(--border-color);
            color: var(--text-secondary);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        /* Custom button styling */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            color: white;
            padding: 10px 20px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            background: var(--gradient-dark);
            border: 1px solid var(--dark-border);
            backdrop-filter: blur(10px);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
        
        .card-header {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), transparent);
            color: var(--primary-color);
            border-bottom: 1px solid var(--dark-border);
            position: relative;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.5rem;
            width: 60px;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 3px;
        }
        
        /* Hamburger menu animation */
        .hamburger {
            cursor: pointer;
            padding: 5px;
            width: 30px;
            height: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .hamburger .line {
            width: 25px;
            height: 3px;
            background-color: var(--primary-color);
            display: block;
            margin: 3px 0;
            transition: all 0.3s ease-in-out;
            border-radius: 2px;
        }
        
        .hamburger:hover .line {
            background-color: var(--gold-color);
            transform: scale(1.1);
        }
        
        /* Active hamburger state */
        .hamburger.active .line:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        
        .hamburger.active .line:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger.active .line:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-color);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
        
        /* Card styling for theme support */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .card-header {
            background: var(--gradient-bg);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        /* Logo styling */
        .logo-abbr {
            filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.5));
            transition: all 0.3s;
            height: 50px;
        }
        
        .logo-abbr:hover {
            filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.8));
            transform: scale(1.05);
        }
        
        /* Animation for page load */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .main-container {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .quixnav {
                position: fixed;
                left: -300px;
                top: 80px;
                height: calc(100vh - 80px);
                z-index: 1000;
                transition: all 0.3s;
                width: 280px;
            }
            
            .quixnav.show {
                left: 0;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.5);
            }
            
            .content-body {
                padding: 15px;
            }
            
            /* Overlay for mobile sidebar */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
        }
        
        @media (max-width: 576px) {
            .brand-title {
                font-size: 1.5rem;
            }
        }
        
        /* Glassmorphism effect */
        .glass-effect {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body>
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

        <!-- Nav header -->
        <div class="nav-header d-flex align-items-center justify-content-between px-3">
            <a href="{{ route('admin.home') }}" class="brand-logo d-flex align-items-center gap-3">
                <img class="logo-abbr" src="{{ asset('images/halalytics.png') }}" alt="Halalytics Logo">
                <span class="brand-title">Halalytics</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <!-- Theme Switcher Button -->
                <button class="theme-switcher" id="themeSwitcher" title="Toggle Theme">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>
                <div class="nav-control">
                    <div class="hamburger">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-end">
                        <!-- Header kosong, hanya untuk spacing -->
                    </div>
                </nav>
            </div>
        </div>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="quixnav">
            <div class="quixnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Menu Utama</li>
                    <li>
                        <a href="{{ route('admin.home') }}"><i class="bi bi-speedometer2 menu-icon"></i><span class="nav-text">Dashboard</span></a>
                    </li>
                    <li>
                        <a href="{{ url('admin/user') }}"><i class="bi bi-people menu-icon"></i><span class="nav-text">Data User</span></a>
                    </li>
                    <li>
                        <a href="{{ url('admin/product') }}"><i class="bi bi-basket menu-icon"></i><span class="nav-text">Data Produk</span></a>
                    </li>
                    <li>
                        <a href="{{ url('admin/scan') }}"><i class="bi bi-search menu-icon"></i><span class="nav-text">Data Scan</span></a>
                    </li>
                    <li>
                        <a href="{{ url('admin/kategori') }}"><i class="bi bi-list menu-icon"></i><span class="nav-text">Kategori Produk</span></a>
                    </li>
                    <li>
                        <a href="{{ url('admin/reports') }}"><i class="bi bi-bar-chart menu-icon"></i><span class="nav-text">Laporan & Statistik</span></a>
                    </li>
                    
                    <li class="nav-label mt-4">Website Promo</li>
                    <li>
                        <a href="{{ route('admin.promo.blog.index') }}"><i class="bi bi-journal-text menu-icon"></i><span class="nav-text">Kelola Artikel</span></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.promo.messages.index') }}"><i class="bi bi-envelope menu-icon"></i><span class="nav-text">Pesan Masuk</span></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.promo.settings.index') }}"><i class="bi bi-gear menu-icon"></i><span class="nav-text">Pengaturan Web</span></a>
                    </li>
                    
                    <!-- Logout Item di Bawah -->
                    <li class="nav-label logout-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right menu-icon"></i>
                                <span class="nav-text">Logout</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content body -->
        <div class="content-body">
            <div class="main-container">
		        @yield('isi')
	        </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="copyright">
                <p>© {{ date('Y') }} Halalytics - Admin Dashboard. All rights reserved.</p>
            </div>
        </div>

    </div>
    

    <!-- Scripts -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('js/quixnav-init.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('vendor/chartist/js/chartist.min.js') }}"></script>
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/pg-calendar/js/pignose.calendar.min.js') }}"></script>
    <script src="{{ asset('js/dashboard/dashboard-2.js') }}"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme Switcher JS -->
    <script src="{{ asset('js/theme-switcher.js') }}"></script>
    
    <script>
        // Custom script for sidebar toggle on mobile
        document.querySelector('.hamburger').addEventListener('click', function() {
            this.classList.toggle('active');
            document.querySelector('.quixnav').classList.toggle('show');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        });
        
        // Close sidebar when clicking on overlay
        document.querySelector('.sidebar-overlay').addEventListener('click', function() {
            document.querySelector('.hamburger').classList.remove('active');
            document.querySelector('.quixnav').classList.remove('show');
            this.classList.remove('active');
        });
        
        // Close sidebar when clicking on a menu item (mobile)
        document.querySelectorAll('.metismenu a').forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    document.querySelector('.hamburger').classList.remove('active');
                    document.querySelector('.quixnav').classList.remove('show');
                    document.querySelector('.sidebar-overlay').classList.remove('active');
                }
            });
        });
        
        // Preloader
        window.addEventListener('load', function() {
            document.getElementById('preloader').style.display = 'none';
        });
        
        // Active menu item highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname;
            const menuItems = document.querySelectorAll('.metismenu a');
            
            menuItems.forEach(item => {
                if (item.getAttribute('href') === currentPage) {
                    item.classList.add('mm-active');
                }
            });
        });
    </script>
</body>
</html>

