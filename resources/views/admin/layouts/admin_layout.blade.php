<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Halalytics Admin')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#004D40",
                        "primary-dark": "#00372e",
                        "primary-soft": "#E0F2F1",
                        "accent": "#26A69A",
                        "background-light": "#F4F9F8",
                        "background-dark": "#1f2938",
                        "emerald-halal": "#059669",
                        "amber-syubhat": "#d97706",
                        "red-haram": "#D32F2F",
                        "slate-custom": "#475569"
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        :root {
            --hal-primary: #004D40;
            --hal-primary-dark: #00372e;
            --hal-secondary: #26A69A;
            --hal-container: #E0F2F1;
            --hal-background: #F4F9F8;
            --hal-surface: #FFFFFF;
            --hal-error: #D32F2F;
        }

        body { font-family: 'Manrope', sans-serif; }
        .chart-gradient {
            background: linear-gradient(180deg, rgba(38, 166, 154, 0.16) 0%, rgba(38, 166, 154, 0) 100%);
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom animations */
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .animate-pulse-slow { animation: pulse-slow 2s ease-in-out infinite; }
        
        /* Sidebar active indicator */
        .nav-active {
            background: rgba(38, 166, 154, 0.14);
            color: var(--hal-primary);
            font-weight: 700;
            box-shadow: inset 0 0 0 1px rgba(38, 166, 154, 0.12);
        }
        
        /* Badge styles */
        .badge-halal { background: #dcfce7; color: #059669; }
        .badge-syubhat { background: #fef3c7; color: #d97706; }
        .badge-haram { background: #fee2e2; color: #dc2626; }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-active { background: #dcfce7; color: #059669; }
        .badge-blocked { background: #fee2e2; color: #dc2626; }
        
        /* Toggle switch */
        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.3s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: var(--hal-secondary);
        }
        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }
        .surface-card {
            background: var(--hal-surface);
            border: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: 0 20px 45px rgba(0, 77, 64, 0.06);
        }
        .depth-card {
            position: relative;
            transform-style: preserve-3d;
            transition: transform .25s ease, box-shadow .25s ease;
            box-shadow: 0 18px 40px rgba(0, 77, 64, 0.08);
        }
        .depth-card:hover {
            transform: translateY(-4px) rotateX(3deg);
            box-shadow: 0 26px 54px rgba(0, 77, 64, 0.12);
        }
        .metric-card {
            color: white;
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: 0 18px 38px rgba(0, 77, 64, 0.18);
        }
        .metric-card--primary {
            background: linear-gradient(145deg, #004D40, #11695b);
        }
        .metric-card--accent {
            background: linear-gradient(145deg, #26A69A, #4db6ac);
        }
        .metric-card--soft {
            background: linear-gradient(145deg, #0f7f73, #26A69A);
        }
        .metric-card--danger {
            background: linear-gradient(145deg, #D32F2F, #ef5350);
        }
        ::view-transition-group(*),
        ::view-transition-old(*),
        ::view-transition-new(*) {
            animation-duration: 0.25s;
            animation-timing-function: cubic-bezier(0.19, 1, 0.22, 1);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-200">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar Navigation -->
    <aside class="w-64 flex-shrink-0 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="p-6 flex items-center space-x-3">
            <div class="w-8 h-8 bg-primary rounded-xl shadow-lg shadow-emerald-900/20 flex items-center justify-center">
                <span class="material-icons-round text-white text-xl">qr_code_scanner</span>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-800 dark:text-white">Halalytics</h1>
        </div>
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto mt-4">
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.dashboard') }}">
                <span class="material-icons-round text-[20px]">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.user*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.user.index') }}">
                <span class="material-icons-round text-[20px]">group</span>
                <span class="text-sm flex-1">Users</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">{{ number_format($global_user_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.product*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.product.index') }}">
                <span class="material-icons-round text-[20px]">inventory_2</span>
                <span class="text-sm flex-1">Products</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">{{ number_format($global_product_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.requests*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.requests.index') }}">
                <span class="material-icons-round text-[20px]">assignment</span>
                <span class="text-sm flex-1">Requests</span>
                <span class="text-[10px] bg-amber-100 dark:bg-amber-900/30 px-1.5 py-0.5 rounded-md font-bold text-amber-600 border border-amber-200 dark:border-amber-700">NEW</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.kategori*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.kategori.index') }}">
                <span class="material-icons-round text-[20px]">category</span>
                <span class="text-sm flex-1">Categories</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">{{ number_format($global_category_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.bpom*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.bpom.index') }}">
                <span class="material-symbols-outlined text-[20px]">verified_user</span>
                <span class="text-sm flex-1">BPOM Data</span>
                <span class="text-[10px] bg-primary/10 text-primary px-1.5 py-0.5 rounded-md font-bold border border-primary/20">VERIFY</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.medicines*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.medicines.index') }}">
                <span class="material-icons-round text-[20px]">medication</span>
                <span class="text-sm flex-1">Medicines</span>
                <span class="text-[10px] bg-primary/10 dark:bg-primary/15 px-1.5 py-0.5 rounded-md font-bold text-primary border border-primary/20 dark:border-primary/30">{{ number_format($global_medicine_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.cosmetics*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.cosmetics.index') }}">
                <span class="material-icons-round text-[20px]">spa</span>
                <span class="text-sm flex-1">Cosmetics</span>
                <span class="text-[10px] bg-pink-100 dark:bg-pink-900/30 px-1.5 py-0.5 rounded-md font-bold text-pink-600 border border-pink-200 dark:border-pink-700">{{ number_format($global_cosmetic_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.ingredients*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.ingredients.index') }}">
                <span class="material-icons-round text-[20px]">science</span>
                <span class="text-sm flex-1">Ingredients</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">NEW</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.banner*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.banner') }}">
                <span class="material-icons-round text-[20px]">view_carousel</span>
                <span class="text-sm flex-1">Banner Slider</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">{{ number_format($global_banner_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.campaigns*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.campaigns.index') }}">
                <span class="material-icons-round text-[20px]">campaign</span>
                <span class="text-sm flex-1">Campaigns</span>
                <span class="text-[10px] bg-emerald-100 dark:bg-emerald-900/30 px-1.5 py-0.5 rounded-md font-bold text-emerald-600 border border-emerald-200 dark:border-emerald-700">FCM</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.promo.blog*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.promo.blog.index') }}">
                <span class="material-icons-round text-[20px]">article</span>
                <span class="text-sm flex-1">Articles</span>
                <span class="text-[10px] bg-emerald-100 dark:bg-emerald-900/30 px-1.5 py-0.5 rounded-md font-bold text-emerald-600 border border-emerald-200 dark:border-emerald-700">CMS</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.street-foods*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.street-foods.index') }}">
                <span class="material-icons-round text-[20px]">restaurant</span>
                <span class="text-sm flex-1">Street Foods</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">{{ number_format($global_street_food_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.forbidden*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.forbidden.index') }}">
                <span class="material-icons-round text-[20px]">block</span>
                <span class="text-sm flex-1">Forbidden Ingredients</span>
                <span class="text-[10px] bg-rose-100 dark:bg-rose-900/30 px-1.5 py-0.5 rounded-md font-bold text-rose-500 border border-rose-200 dark:border-rose-800">SAFETY</span>
            </a>
            
            <div class="pt-4 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Expansion Modules</div>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('halal-products*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('halal-products.index') }}">
                <span class="material-icons-round text-[20px]">verified</span>
                <span class="text-sm flex-1">Halal Products</span>
            </a>

            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.ocr*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.ocr.index') }}">
                <span class="material-icons-round text-[20px]">document_scanner</span>
                <span class="text-sm flex-1">OCR Management</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.notifications*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.notifications.index') }}">
                <span class="material-icons-round text-[20px]">notifications_active</span>
                <span class="text-sm flex-1">Notifications</span>
            </a>
            <div class="pt-4 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Activity & Reports</div>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.scan*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.scan.index') }}">
                <span class="material-icons-round text-[20px]">history</span>
                <span class="text-sm flex-1">Scan History</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-bold text-slate-500 border border-slate-200 dark:border-slate-700">{{ number_format($global_scan_count) }}</span>
            </a>
            <a class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.report*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all" href="{{ route('admin.report.index') }}">
                <span class="material-icons-round text-[20px]">assessment</span>
                <span class="text-sm flex-1">Product Reports</span>
                @if($global_report_count > 0)
                <span class="text-[10px] bg-red-100 dark:bg-red-900/30 px-1.5 py-0.5 rounded-md font-bold text-red-600 border border-red-200 dark:border-red-800/50 animate-pulse-slow">{{ number_format($global_report_count) }}</span>
                @endif
            </a>
        </nav>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-primary flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ Auth::user()->full_name ?? Auth::user()->username }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-icons-round text-lg">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    
    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto scrollbar-hide">
        <!-- Top Bar -->
        <header class="h-16 bg-white/95 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 backdrop-blur flex items-center justify-between px-8 sticky top-0 z-10">
            <div class="flex items-center space-x-2 text-sm">
                @hasSection('breadcrumb')
                    @yield('breadcrumb')
                @else
                    <span class="text-slate-400">@yield('breadcrumb-parent', 'Dashboard')</span>
                    <span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">@yield('breadcrumb-current', 'Overview')</span>
                @endif
            </div>
            <div class="flex items-center space-x-6">
                <!-- Global Search with Dropdown -->
                <div class="relative" id="searchContainer">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <span class="material-icons-round text-lg">search</span>
                    </span>
                    <input class="pl-10 pr-4 py-1.5 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Quick search..." type="text" id="globalSearch" autocomplete="off"/>
                    <!-- Search Results Dropdown -->
                    <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 hidden z-50 max-h-96 overflow-y-auto">
                        <div id="searchResultsContent"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Notifications Bell with Dropdown -->
                    <div class="relative" id="notificationContainer">
                        <button class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" id="notificationBtn">
                            <span class="material-icons-round">notifications</span>
                            <span id="notificationBadge" class="absolute top-1 right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center hidden">0</span>
                        </button>
                        <!-- Notifications Dropdown -->
                        <div id="notificationDropdown" class="absolute top-full right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 hidden z-50">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                <span class="font-bold text-sm">Notifications</span>
                                <button id="markAllReadBtn" class="text-xs text-primary hover:underline">Mark all read</button>
                            </div>
                            <div id="notificationList" class="max-h-80 overflow-y-auto">
                                <div class="p-4 text-center text-slate-400 text-sm">Loading...</div>
                            </div>
                        </div>
                    </div>
                    <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons-round">dark_mode</span>
                    </button>
                </div>
            </div>
        </header>
        
        <div class="p-8 max-w-7xl mx-auto">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center space-x-3">
                <span class="material-icons-round text-emerald-500">check_circle</span>
                <span class="text-emerald-700 dark:text-emerald-300 text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif
            
            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-center space-x-3">
                <span class="material-icons-round text-red-500">error</span>
                <span class="text-red-700 dark:text-red-300 text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif
            
            @yield('content')
        </div>
    </main>
</div>

<script>
    // CSRF token for AJAX requests
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // ==================== GLOBAL SEARCH ====================
    let searchTimeout = null;
    const searchInput = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    const searchResultsContent = document.getElementById('searchResultsContent');
    
    searchInput?.addEventListener('input', function(e) {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetchSearchResults(query);
        }, 300); // Debounce 300ms
    });
    
    async function fetchSearchResults(query) {
        try {
            const response = await fetch(`{{ route('admin.global.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            
            if (data.success) {
                renderSearchResults(data.results, data.total_count);
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }
    
    function renderSearchResults(results, totalCount) {
        if (totalCount === 0) {
            searchResultsContent.innerHTML = `<div class="p-4 text-center text-slate-400 text-sm">No results found</div>`;
            searchResults.classList.remove('hidden');
            return;
        }
        
        let html = '';
        const categories = { products: 'Products', users: 'Users', scans: 'Scans', reports: 'Reports' };
        const icons = { products: 'inventory_2', users: 'person', scans: 'qr_code_scanner', reports: 'flag' };
        
        for (const [key, label] of Object.entries(categories)) {
            if (results[key] && results[key].length > 0) {
                html += `<div class="px-3 py-2 text-xs font-bold text-slate-400 uppercase bg-slate-50 dark:bg-slate-900">${label}</div>`;
                results[key].forEach(item => {
                    html += `
                        <a href="${item.url}" class="flex items-center px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <span class="material-icons-round text-slate-400 mr-3 text-lg">${icons[key]}</span>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate">${item.title}</div>
                                <div class="text-xs text-slate-400 truncate">${item.subtitle || ''}</div>
                            </div>
                            ${item.status ? `<span class="text-xs px-2 py-0.5 rounded-full ${getStatusClass(item.status)}">${item.status}</span>` : ''}
                        </a>
                    `;
                });
            }
        }
        
        searchResultsContent.innerHTML = html;
        searchResults.classList.remove('hidden');
    }
    
    function getStatusClass(status) {
        const statusLower = status.toLowerCase();
        if (['halal', 'verified', 'active', 'admin'].includes(statusLower)) return 'bg-emerald-100 text-emerald-700';
        if (['syubhat', 'diragukan', 'pending'].includes(statusLower)) return 'bg-amber-100 text-amber-700';
        if (['haram', 'tidak halal', 'blocked', 'rejected'].includes(statusLower)) return 'bg-red-100 text-red-700';
        return 'bg-slate-100 text-slate-700';
    }
    
    // Close search on outside click
    document.addEventListener('click', (e) => {
        if (!document.getElementById('searchContainer').contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
    
    // ==================== NOTIFICATIONS ====================
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    // Toggle dropdown
    notificationBtn?.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
        if (!notificationDropdown.classList.contains('hidden')) {
            fetchNotifications();
        }
    });
    
    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!document.getElementById('notificationContainer').contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
    
    // Fetch notification count on page load
    fetchNotificationCount();
    setInterval(fetchNotificationCount, 30000); // Refresh every 30 seconds
    
    async function fetchNotificationCount() {
        try {
            const response = await fetch('/admin/notifications-api/unread-count', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            
            if (data.success) {
                if (data.count > 0) {
                    notificationBadge.textContent = data.count > 9 ? '9+' : data.count;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error fetching notification count:', error);
        }
    }
    
    async function fetchNotifications() {
        try {
            const response = await fetch('/admin/notifications-api/', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                let html = '';
                data.data.forEach(notif => {
                    const isRead = notif.is_read;
                    const detail = notif.detail ? `<div class="text-[11px] text-slate-500 mt-1">${notif.detail}</div>` : '';
                    html += `
                        <div class="p-3 border-b border-slate-100 dark:border-slate-700 ${isRead ? 'opacity-60' : ''} hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer" onclick="openNotification(${notif.id}, '${notif.target_url || '/admin'}')">
                            <div class="flex items-start space-x-3">
                                <span class="material-icons-round text-${notif.color || 'primary'}-500">${notif.icon || 'notifications'}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium ${isRead ? '' : 'font-bold'}">${notif.title}</div>
                                    <div class="text-xs text-slate-400">${notif.message}</div>
                                    ${detail}
                                    <div class="text-[10px] text-slate-300 mt-1">${notif.relative_time || formatTime(notif.created_at)}</div>
                                </div>
                                ${!isRead ? '<span class="w-2 h-2 bg-primary rounded-full flex-shrink-0"></span>' : ''}
                            </div>
                        </div>
                    `;
                });
                notificationList.innerHTML = html;
            } else {
                notificationList.innerHTML = '<div class="p-4 text-center text-slate-400 text-sm">No notifications</div>';
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
            notificationList.innerHTML = '<div class="p-4 text-center text-red-400 text-sm">Error loading notifications</div>';
        }
    }
    
    async function markAsRead(id) {
        try {
            await fetch(`/admin/notifications-api/${id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            });
            fetchNotificationCount();
            fetchNotifications();
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    async function openNotification(id, targetUrl) {
        await markAsRead(id);
        if (targetUrl && targetUrl !== '/admin' && targetUrl !== 'null' && targetUrl !== '') {
            window.location.href = targetUrl;
        }
    }
    
    markAllReadBtn?.addEventListener('click', async function() {
        try {
            await fetch('/admin/notifications-api/read-all', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            });
            fetchNotificationCount();
            fetchNotifications();
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    });
    
    function formatTime(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diff = (now - date) / 1000;
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff/3600)}h ago`;
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }
    
    // ==================== DARK MODE ====================
    if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
    }
    
    document.querySelector('[onclick*="dark"]')?.addEventListener('click', function() {
        localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
    });
</script>
@stack('scripts')
</body>
</html>
