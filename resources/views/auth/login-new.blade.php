<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - Halalytics Admin</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#00bbc2", 
                        "primary-dark": "#009fa5",
                        "background-light": "#f9fafb",
                        "background-dark": "#111827",
                    },
                    fontFamily: {
                        display: ["Manrope", "sans-serif"],
                        sans: ["Manrope", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        xl: "0.75rem",
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                    },
                },
            },
        };
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
        }
    </script>
    <style>
        .hero-pattern {
            background-color: #00bbc2;
            background-image: linear-gradient(135deg, rgba(0, 187, 194, 0.95) 0%, rgba(0, 159, 165, 0.9) 100%), url('https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=2574&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex items-center justify-center p-0 md:p-4 transition-colors duration-300 font-display">
    <div class="w-full max-w-6xl min-h-[700px] flex flex-col md:flex-row shadow-2xl rounded-none md:rounded-3xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
        <!-- Left Side: Hero -->
        <div class="hero-pattern hidden md:flex w-1/2 p-12 flex-col justify-between text-white relative">
            <div class="z-10">
                <div class="flex items-center gap-3 mb-8">
                    <span class="material-icons-round text-4xl bg-white/20 p-2 rounded-xl backdrop-blur-sm">qr_code_scanner</span>
                    <h1 class="text-4xl font-extrabold tracking-tight">Halalytics</h1>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-3xl max-w-lg">
                    <p class="text-xl font-medium mb-6 leading-relaxed">Your trusted companion for halal and healthy food choices. Scan products instantly to verify their halal status.</p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3">
                            <span class="material-icons-round text-emerald-300">check_circle</span>
                            <span>Instant halal verification</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-icons-round text-emerald-300">check_circle</span>
                            <span>Comprehensive product database</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-icons-round text-emerald-300">check_circle</span>
                            <span>AI-powered ingredients analysis</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="z-10">
                <p class="text-sm font-semibold uppercase tracking-wider mb-4 opacity-80">System Status</p>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-bold">All Systems Operational</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 flex flex-col p-8 md:p-16 justify-center relative bg-white dark:bg-gray-900">
            <button class="absolute top-8 right-8 p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" onclick="toggleDarkMode()">
                <span class="material-icons-round dark:hidden">dark_mode</span>
                <span class="material-icons-round hidden dark:block">light_mode</span>
            </button>
            <div class="w-full max-w-md mx-auto">
                <div class="mb-10 text-center md:text-left">
                    <p class="text-primary font-bold mb-2 uppercase tracking-wide text-sm">Admin Portal</p>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Login to Your Account</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Welcome back! Please enter your details.</p>
                </div>
                
                @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl flex items-center gap-3">
                    <span class="material-icons-round">error_outline</span>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
                @endif
                
                <form action="{{ route('actionLogin') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2" for="username">
                            <span class="material-icons-round text-sm text-gray-400">person</span> Username
                        </label>
                        <input class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="username" name="username" placeholder="Enter your username" type="text" required autofocus />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2" for="password">
                            <span class="material-icons-round text-sm text-gray-400">lock</span> Password
                        </label>
                        <div class="relative">
                            <input class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="password" name="password" placeholder="••••••••" type="password" required />
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';">
                                <span class="material-icons-round">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="rounded text-primary focus:ring-primary dark:bg-gray-800 border-gray-300 dark:border-gray-700" type="checkbox" name="remember" />
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Remember me</span>
                        </label>
                        <a class="text-sm font-bold text-primary hover:text-primary-dark transition-colors" href="#">Forgot password?</a>
                    </div>
                    <button class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-all transform active:scale-[0.98]" type="submit">
                        <span class="material-icons-round text-xl">login</span> Sign In
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Don't have an admin account? 
                        <a class="text-primary font-bold hover:underline" href="#">Contact Super Admin</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
