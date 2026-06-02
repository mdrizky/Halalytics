<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Expert Dashboard - Halalytics</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#004D40",
                        "accent": "#26A69A",
                        "background-light": "#F4F9F8",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans", "Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        .surface-card { background: white; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0, 77, 64, 0.05); }
        .metric-card { background: linear-gradient(135deg, #004D40, #26A69A); color: white; border-radius: 1.5rem; padding: 1.5rem; }
    </style>
</head>
<body class="bg-background-light text-slate-800 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- Expert Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-100 flex flex-col">
            <div class="p-6 flex items-center space-x-3">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white font-bold">H</div>
                <h1 class="text-lg font-bold tracking-tight text-primary">Halalytics Expert</h1>
            </div>
            
            <nav class="flex-1 px-4 space-y-1 mt-4">
                <a href="{{ route('expert.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('expert.dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 transition-all' }}">
                    <span class="material-icons-round">dashboard</span>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="{{ route('expert.patients') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('expert.patients') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 transition-all' }}">
                    <span class="material-icons-round">people</span>
                    <span class="text-sm">Daftar Pasien</span>
                </a>
                <a href="{{ route('expert.consultations') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('expert.consultations') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 transition-all' }}">
                    <span class="material-icons-round">chat</span>
                    <span class="text-sm">Konsultasi</span>
                </a>
                <a href="{{ route('expert.meal-plans') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('expert.meal-plans') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 transition-all' }}">
                    <span class="material-icons-round">restaurant_menu</span>
                    <span class="text-sm">Meal Plan</span>
                </a>

                @php $isScheduleActive = request()->routeIs('expert.schedule*'); @endphp
                <div class="relative">
                    <button type="button" onclick="document.getElementById('expertMoreMenu').classList.toggle('hidden');" class="w-full flex items-center justify-between px-4 py-3 rounded-xl {{ $isScheduleActive ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 transition-all' }}">
                        <div class="flex items-center space-x-3">
                            <span class="material-icons-round text-[20px]">more_horiz</span>
                            <span class="text-sm">Lainnya</span>
                        </div>
                        <span class="material-icons-round text-[18px]">expand_more</span>
                    </button>
                    <div id="expertMoreMenu" class="mt-1 space-y-1 pl-11 hidden">
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-slate-500 hover:text-primary hover:bg-slate-50 transition-all text-sm">
                            <span class="material-icons-round text-[18px]">message</span>
                            <span class="text-sm">Pesan</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-slate-500 hover:text-primary hover:bg-slate-50 transition-all text-sm">
                            <span class="material-icons-round text-[18px]">calendar_month</span>
                            <span class="text-sm">Janji Temu</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-slate-500 hover:text-primary hover:bg-slate-50 transition-all text-sm">
                            <span class="material-icons-round text-[18px]">settings</span>
                            <span class="text-sm">Pengaturan</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-4 border-t border-slate-50">
                <div class="flex items-center p-3 rounded-2xl bg-slate-50">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->full_name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Expert</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-500">
                            <span class="material-icons-round text-sm">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-800">Selamat Datang, Expert!</h2>
                    <p class="text-slate-500 text-sm mt-1">Pantau perkembangan kesehatan pasien Anda hari ini.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 bg-white rounded-xl shadow-sm text-slate-400"><span class="material-icons-round">notifications</span></button>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ now()->format('d M Y') }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black">Today</p>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
</body>
</html>
