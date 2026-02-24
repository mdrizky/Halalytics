<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Halalytics Admin Portal</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                        display: ["Outfit", "sans-serif"],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        dark: {
                            bg: '#0f172a',
                            card: '#1e293b',
                            input: '#334155',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'blob': 'blob 7s infinite',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.5s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: "Inter", sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-input {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }
        .dark .glass-input {
            background: rgba(15, 23, 42, 0.5);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4 relative overflow-hidden selection:bg-primary-500 selection:text-white">
    
    <!-- Animated Background Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 left-[-20%] w-[500px] h-[500px] bg-primary-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 right-[-20%] w-[500px] h-[500px] bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-[20%] w-[500px] h-[500px] bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
    </div>

    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8 z-10 items-center">
        
        <!-- Left Side: Branding (Hidden on mobile) -->
        <div class="hidden md:flex flex-col justify-center space-y-8 p-8 animate-fade-in text-gray-800 dark:text-white">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/30 dark:bg-white/5 border border-white/20 backdrop-blur-md shadow-sm">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-semibold tracking-wide uppercase text-emerald-700 dark:text-emerald-400">Admin Portal v2.0</span>
                </div>
                <h1 class="text-6xl font-extrabold font-display leading-tight">
                    Halalytics <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-green-500 dark:from-emerald-400 dark:to-green-300">Intelligent Platform</span>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-md leading-relaxed">
                    Manage your halal database, analyze user trends, and oversee ingredient verification with our next-gen AI-powered dashboard.
                </p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="glass-card p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <span class="material-icons-round text-emerald-500 text-3xl mb-2">qr_code_scanner</span>
                    <h3 class="font-bold text-lg font-display">Global Scanner</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Real-time product verification stats</p>
                </div>
                <div class="glass-card p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <span class="material-icons-round text-blue-500 text-3xl mb-2">smart_toy</span>
                    <h3 class="font-bold text-lg font-display">AI Insight</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Advanced symptom & meal analysis</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full max-w-md mx-auto animate-slide-up">
            <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 relative overflow-hidden">
                <!-- Decorative Top Gradient -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-500 via-green-400 to-teal-400"></div>

                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white font-display text-center mb-2">Selamat Datang</h2>
                    <p class="text-center text-gray-500 dark:text-gray-400 text-sm">Masuk untuk mengakses dashboard admin Anda</p>
                </div>

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50/80 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl backdrop-blur-sm shadow-sm flex items-start gap-3 animate-slide-up">
                    <span class="material-icons-round text-red-500 mt-0.5">error_outline</span>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                @endif
                
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50/80 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl backdrop-blur-sm shadow-sm flex items-start gap-3 animate-slide-up">
                    <span class="material-icons-round text-emerald-500 mt-0.5">check_circle</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                @endif

                <form class="space-y-6" method="POST" action="{{ route('actionLogin') }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 pl-1" for="username">Nama Pengguna</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-icons-round text-gray-400 group-focus-within:text-emerald-500 transition-colors">person</span>
                            </div>
                            <input class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 glass-input text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium outline-none" id="username" name="username" placeholder="Masukkan nama pengguna" type="text" required value="{{ old('username') }}"/>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-2 pl-1">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300" for="password">Kata Sandi</label>
                            <a class="text-xs font-semibold text-emerald-600 hover:text-emerald-500 transition-colors" href="#">Lupa Kata Sandi?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-icons-round text-gray-400 group-focus-within:text-emerald-500 transition-colors">lock</span>
                            </div>
                            <input class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 glass-input text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium outline-none" id="password" name="password" placeholder="••••••••" type="password" required/>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">Ingat saya</span>
                        </label>
                    </div>

                    <button class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-600/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 group" type="submit">
                        <span class="material-icons-round group-hover:animate-pulse">login</span> 
                        Masuk ke Dashboard
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 text-center">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-4">Atau masuk dengan</p>
                    <div class="flex justify-center gap-4">
                        <a href="{{ url('/auth/google') }}" class="p-3 rounded-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-all group">
                            <img alt="Google" class="w-6 h-6 group-hover:scale-110 transition-transform" src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png"/>
                        </a>
                        <a href="{{ url('/auth/facebook') }}" class="p-3 rounded-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-all group">
                            <svg class="w-6 h-6 text-[#1877F2] fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-8">
                &copy; {{ date('Y') }} Halalytics Inc. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>