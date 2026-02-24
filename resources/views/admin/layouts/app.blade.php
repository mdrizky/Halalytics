<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Halalytics Admin')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00bbc2",
                        "primary-dark": "#009fa5",
                        "background-light": "#f9fafb",
                        "background-dark": "#1f2938",
                        "emerald-halal": "#059669",
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
        body { 
            font-family: 'Manrope', sans-serif; 
        }
        .chart-gradient {
            background: linear-gradient(180deg, rgba(0, 187, 194, 0.1) 0%, rgba(0, 187, 194, 0) 100%);
        }
        .scrollbar-hide::-webkit-scrollbar { 
            display: none; 
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Active nav link */
        .nav-active {
            background: rgba(0, 187, 194, 0.1);
            color: #00bbc2;
            font-weight: 700;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-200">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-64 flex-shrink-0 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col" id="sidebar">
            <!-- Logo -->
            <div class="p-6 flex items-center space-x-3">
                <div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
                    <span class="material-icons-round text-white text-xl">qr_code_scanner</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight text-slate-800 dark:text-white">Halalytics</h1>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto mt-4 scrollbar-hide">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">dashboard</span>
                    <span class="text-sm">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.user') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.user*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">group</span>
                    <span class="text-sm">Users</span>
                </a>
                
                <a href="{{ route('admin.product.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.product*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">inventory_2</span>
                    <span class="text-sm">Products</span>
                </a>
                
                <a href="{{ route('admin.kategori') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.kategori*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">category</span>
                    <span class="text-sm">Categories</span>
                </a>
                
                <!-- Divider -->
                <div class="pt-4 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Activity & Reports</div>
                
                <a href="{{ route('admin.scan.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.scan*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">history</span>
                    <span class="text-sm">Scan History</span>
                </a>
                
                <a href="{{ route('admin.report.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.report*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">description</span>
                    <span class="text-sm">Reports</span>
                </a>
                
                <a href="{{ route('admin.banner') ?? '#' }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.banner*') ? 'nav-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }} transition-all">
                    <span class="material-icons-round text-[20px]">image</span>
                    <span class="text-sm">Banners</span>
                </a>
            </nav>
            
            <!-- User Profile at Bottom -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-primary flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ Auth::user()->full_name ?? Auth::user()->username }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->role ?? 'Admin' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Logout">
                            <span class="material-icons-round text-lg">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto scrollbar-hide">
            <!-- Top Bar -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 sticky top-0 z-10">
                <!-- Breadcrumb -->
                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-slate-400">@yield('breadcrumb-parent', 'Dashboard')</span>
                    <span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">@yield('breadcrumb-current', 'Overview')</span>
                </div>
                
                <div class="flex items-center space-x-6">
                    <!-- Search -->
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <span class="material-icons-round text-lg">search</span>
                        </span>
                        <input type="text" placeholder="Quick search..." class="pl-10 pr-4 py-1.5 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all">
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-3">
                        <button class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" id="notificationBtn">
                            <span class="material-icons-round">notifications</span>
                            <span id="unreadBadge" class="hidden absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border-2 border-white dark:border-slate-900 rounded-full animate-pulse"></span>
                        </button>
                        <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" onclick="document.documentElement.classList.toggle('dark')">
                            <span class="material-icons-round">dark_mode</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="p-8 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
    
    <script>
        // Real-time Notification Polling
        let currentUnreadCount = 0;
        
        async function checkNotifications() {
            try {
                const response = await fetch('/admin/notifications-api/unread-count');
                const result = await response.json();
                
                if (result.success) {
                    const badge = document.getElementById('unreadBadge');
                    if (result.count > 0) {
                        badge.classList.remove('hidden');
                        if (result.count > currentUnreadCount) {
                            // Notify user of new activity (could add toast here)
                            console.log('New notification received!');
                        }
                    } else {
                        badge.classList.add('hidden');
                    }
                    currentUnreadCount = result.count;
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }
        
        // Poll every 30 seconds
        setInterval(checkNotifications, 30000);
        checkNotifications(); // Initial check
    </script>
</body>
</html>
