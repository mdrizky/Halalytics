<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName = $settings['site_name'] ?? 'Halalytics';
        $defaultDescription = $settings['site_description'] ?? 'AI-powered halal, health, and community intelligence platform';
        $metaDescription = trim((string) $__env->yieldContent('description', $defaultDescription));
        $metaKeywords = trim((string) $__env->yieldContent('keywords', 'halal scanner, cek halal, interaksi obat, health score, BPOM'));
        $canonicalUrl = trim((string) $__env->yieldContent('canonical', url()->current()));
        $defaultOgImage = asset('images/logo_halalytics.png');
        $ogImage = trim((string) $__env->yieldContent('og_image', $defaultOgImage));
        $pageTitle = trim((string) $__env->yieldContent('title', $siteName));
    @endphp

    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_halalytics.png') }}">
    <title>{{ $pageTitle }}</title>

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --promo-primary: #004D40;
            --promo-primary-deep: #00372e;
            --promo-secondary: #26A69A;
            --promo-container: #E0F2F1;
            --promo-background: #F4F9F8;
            --promo-surface: #FFFFFF;
            --promo-error: #D32F2F;
            --promo-ink: #163832;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--promo-background); color: var(--promo-ink); }
        h1, h2, h3, h4, .font-brand { font-family: 'Space Grotesk', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #004D40 0%, #26A69A 100%); }
        .gradient-text { background: linear-gradient(135deg, #004D40, #26A69A); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px) rotateX(2deg); box-shadow: 0 24px 48px rgba(0, 77, 64, 0.14); }
        .promo-link { color: #4b5563; transition: color .2s ease; }
        .promo-link:hover, .promo-link.active { color: var(--promo-primary); }
        .promo-btn {
            background: var(--promo-primary);
            color: white;
            transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
            box-shadow: 0 14px 30px rgba(0, 77, 64, 0.16);
        }
        .promo-btn:hover { background: var(--promo-primary-deep); transform: translateY(-1px); }
        .promo-depth {
            transform-style: preserve-3d;
            box-shadow: 0 22px 50px rgba(0, 77, 64, 0.10);
        }
    </style>
    @stack('head')
    @yield('styles')
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur-sm shadow-sm border-b border-[#d8ebe8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="bg-white rounded-lg p-1.5 shadow-sm border border-[#e0f2f1]">
                        <img src="{{ asset('images/logo_halalytics.png') }}" alt="Halalytics Logo" class="h-8 w-auto object-contain">
                    </div>
                    <span class="font-brand font-bold text-xl text-[#163832]">
                        {{ $settings['site_name'] ?? 'Halalytics' }}
                    </span>
                </a>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="promo-link font-medium {{ request()->routeIs('home') ? 'active' : '' }}">
                        Beranda
                    </a>
                    
                    <!-- Dropdown Perawatan Khusus -->
                    <div class="relative group">
                        <button class="promo-link font-medium flex items-center gap-1 focus:outline-none">
                            Perawatan Khusus
                            <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="p-2 space-y-1">
                                <a href="{{ route('specialized.show', 'diabetes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Diabetes Care</a>
                                <a href="{{ route('specialized.show', 'heart') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Heart Health</a>
                                <a href="{{ route('specialized.show', 'mental') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Mental Health Center</a>
                                <a href="{{ route('specialized.show', 'skin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Haloskin (Skin Care)</a>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Cek Kesehatan -->
                    <div class="relative group">
                        <button class="promo-link font-medium flex items-center gap-1 focus:outline-none">
                            Cek Kesehatan Mandiri
                            <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="p-2 space-y-1">
                                <a href="{{ route('home') }}#health-tools" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Kalkulator Nutrisi & BMR</a>
                                <a href="{{ route('home') }}#health-tools" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Target Air Minum</a>
                                <a href="{{ route('home') }}#health-tools" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Skrining Risiko Lifestyle</a>
                                <a href="{{ route('home') }}#health-tools" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Kalkulator Kehamilan</a>
                                <a href="{{ route('home') }}#health-tools" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg">Tes Ketajaman Mata</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('blog.index') }}" class="promo-link font-medium {{ request()->routeIs('blog*') ? 'active' : '' }}">
                        Kamus Kesehatan A-Z
                    </a>
                    
                    <a href="{{ route('download') }}" class="promo-btn font-semibold px-5 py-2 rounded-full">
                        Download APK
                    </a>
                </div>

                <!-- Hamburger Mobile -->
                <button id="mobileMenuBtn" class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Menu Mobile -->
            <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
                <a href="{{ route('home') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">Beranda</a>
                <a href="{{ route('features') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">Fitur</a>
                <a href="{{ route('blog.index') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">Blog</a>
                <a href="{{ route('about') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">Tentang</a>
                <a href="{{ route('download') }}" class="block py-2 px-4 promo-btn rounded-lg font-semibold text-center">Download APK</a>
            </div>
        </div>
    </nav>

    <!-- ===== KONTEN UTAMA ===== -->
    <main class="pt-16">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 pt-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">&times;</button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 pt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 pt-3">
            @include('components.medical-ai-disclaimer-banner')
        </div>

        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-[#0e2e29] text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-white rounded-xl p-2">
                            <img src="{{ asset('images/logo_halalytics.png') }}" alt="Halalytics Logo" class="h-10 w-auto object-contain">
                        </div>
                        <span class="font-bold text-xl">{{ $settings['site_name'] ?? 'Halalytics' }}</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        {{ $settings['site_description'] ?? 'AI-powered halal, health, OCR, BPOM, and donor community platform.' }}
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-semibold mb-4 text-gray-300">Navigasi</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-[var(--promo-secondary)] transition-colors">Beranda</a></li>
                        <li><a href="{{ route('features') }}" class="hover:text-[var(--promo-secondary)] transition-colors">Fitur</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-[var(--promo-secondary)] transition-colors">Blog</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-[var(--promo-secondary)] transition-colors">Tentang</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-[var(--promo-secondary)] transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Download -->
                <div>
                    <h4 class="font-semibold mb-4 text-gray-300">Download</h4>
                    <a href="{{ $settings['playstore_url'] ?? '#' }}" target="_blank"
                       class="inline-flex items-center space-x-2 promo-btn px-4 py-2 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5a1 1 0 010 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/>
                        </svg>
                        <span>Google Play</span>
                    </a>
                    <p class="text-xs text-gray-500 mt-2">
                        Versi {{ $settings['app_version'] ?? '1.0.0' }} • Android
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Halalytics' }}. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Informasi bersifat edukatif, bukan pengganti konsultasi profesional.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
    </script>
    @hasSection('schema')
    <script type="application/ld+json">
{!! trim($__env->yieldContent('schema')) !!}
    </script>
    @endif
    @yield('scripts')
</body>
</html>
