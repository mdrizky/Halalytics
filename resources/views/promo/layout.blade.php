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

    <!-- Swiper.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

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
                        {{ __('messages.nav_home') }}
                    </a>
                    
                    <!-- Dropdown Perawatan Khusus -->
                    <div class="relative group">
                        <button class="promo-link font-medium flex items-center gap-1 focus:outline-none">
                            {{ __('messages.nav_specialized') }}
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
                            {{ __('messages.nav_health_check') }}
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
                        {{ __('messages.nav_blog') }}
                    </a>

                    <!-- Language Selector (Desktop) -->
                    <div class="relative group">
                        <button class="promo-link font-medium flex items-center gap-1 focus:outline-none">
                            @if(app()->getLocale() == 'en')
                                🇺🇸 EN
                            @else
                                🇮🇩 ID
                            @endif
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-36 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="p-2 space-y-1">
                                <a href="{{ route('lang.switch', 'id') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg flex items-center gap-2">
                                    <span>🇮🇩</span> Indonesia
                                </a>
                                <a href="{{ route('lang.switch', 'en') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg flex items-center gap-2">
                                    <span>🇺🇸</span> English
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('download') }}" class="promo-btn font-semibold px-5 py-2 rounded-full">
                        {{ __('messages.nav_download') }}
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
                <a href="{{ route('home') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">{{ __('messages.nav_home') }}</a>
                <a href="{{ route('features') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">Fitur</a>
                <a href="{{ route('blog.index') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">{{ __('messages.nav_blog') }}</a>
                <a href="{{ route('about') }}" class="block py-2 px-4 text-slate-700 hover:bg-[#E0F2F1] rounded-lg">Tentang</a>
                <div class="border-t border-gray-100 pt-2 flex items-center justify-around">
                    <a href="{{ route('lang.switch', 'id') }}" class="block py-2 px-4 text-sm font-semibold {{ app()->getLocale() == 'id' ? 'text-[var(--promo-primary)]' : 'text-slate-500' }}">🇮🇩 Indonesia</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="block py-2 px-4 text-sm font-semibold {{ app()->getLocale() == 'en' ? 'text-[var(--promo-primary)]' : 'text-slate-500' }}">🇺🇸 English</a>
                </div>
                <a href="{{ route('download') }}" class="block py-2 px-4 promo-btn rounded-lg font-semibold text-center">{{ __('messages.nav_download') }}</a>
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
    <footer class="bg-gradient-to-br from-[#061F1B] via-[#092B26] to-[#041613] text-gray-300 border-t border-[#0d3b34] mt-20 relative overflow-hidden">
        <!-- Background light glows -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
                <!-- Brand and About Column -->
                <div class="md:col-span-5 space-y-6">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="bg-white rounded-xl p-2 shadow-md border border-[#e0f2f1]/20">
                            <img src="{{ asset('images/logo_halalytics.png') }}" alt="Halalytics Logo" class="h-10 w-auto object-contain">
                        </div>
                        <span class="font-brand font-bold text-2xl text-white tracking-wide">
                            {{ $settings['site_name'] ?? 'Halalytics' }}
                        </span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                        {{ $settings['site_description'] ?? 'E-Health Super App berbasis AI terintegrasi pertama yang menghubungkan database BPOM, analisis titik kritis halal, skrining nutrisi pintar, dan pencarian donor darah siaga dalam satu genggaman.' }}
                    </p>
                    <!-- Social Media Icons with Premium Hover Transitions -->
                    <div class="flex items-center space-x-4 pt-2">
                        <a href="https://instagram.com" target="_blank" class="w-10 h-10 rounded-xl bg-[#0b3831] border border-[#144f45] flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-500 hover:scale-110 transition-all duration-300 shadow-md shadow-black/10" aria-label="Instagram">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="https://twitter.com" target="_blank" class="w-10 h-10 rounded-xl bg-[#0b3831] border border-[#144f45] flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-500 hover:scale-110 transition-all duration-300 shadow-md shadow-black/10" aria-label="Twitter">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="https://linkedin.com" target="_blank" class="w-10 h-10 rounded-xl bg-[#0b3831] border border-[#144f45] flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-500 hover:scale-110 transition-all duration-300 shadow-md shadow-black/10" aria-label="LinkedIn">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        <a href="https://youtube.com" target="_blank" class="w-10 h-10 rounded-xl bg-[#0b3831] border border-[#144f45] flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-500 hover:scale-110 transition-all duration-300 shadow-md shadow-black/10" aria-label="YouTube">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.107C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.388.511a3.002 3.002 0 00-2.11 2.107C0 8.053 0 12 0 12s0 3.947.502 5.837a3.003 3.003 0 002.11 2.107c1.883.511 9.388.511 9.388.511s7.505 0 9.388-.511a3.002 3.002 0 002.11-2.107C24 15.947 24 12 24 12s0-3.947-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Navigation Links Column -->
                <div class="md:col-span-3 space-y-4">
                    <h4 class="font-semibold text-lg text-white tracking-wide">Navigasi Utama</h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-emerald-400 transition-colors flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('features') }}" class="hover:text-emerald-400 transition-colors flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Fitur Spesialis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('blog.index') }}" class="hover:text-emerald-400 transition-colors flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Blog & Edukasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}" class="hover:text-emerald-400 transition-colors flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tentang Kami
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="hover:text-emerald-400 transition-colors flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Kebijakan Privasi
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Downloads Column -->
                <div class="md:col-span-4 space-y-6">
                    <h4 class="font-semibold text-lg text-white tracking-wide">Dapatkan Aplikasi</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Unduh Halalytics sekarang di Google Play Store untuk memantau kesehatan keluarga secara aman dan terkontrol.
                    </p>
                    <div class="pt-2">
                        <a href="{{ $settings['playstore_url'] ?? '#' }}" target="_blank"
                           class="inline-flex items-center space-x-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold px-6 py-3.5 rounded-2xl text-sm transition-all duration-300 shadow-lg shadow-emerald-900/30">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5a1 1 0 010 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/>
                            </svg>
                            <span>Download di Google Play</span>
                        </a>
                        <p class="text-xs text-gray-500 mt-3 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Versi {{ $settings['app_version'] ?? '1.0.0' }} • Berbasis Android
                        </p>
                    </div>
                </div>
            </div>

            <!-- Medical & AI Disclaimer Banner -->
            <div class="border-t border-[#0d3b34] mt-12 pt-8">
                <div class="bg-[#08221E] border border-[#144b41] rounded-2xl p-6 flex flex-col md:flex-row items-start gap-4 shadow-inner">
                    <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-400 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-white mb-1 uppercase tracking-wider">Pernyataan Penting AI & Medis (Disclaimer)</h5>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Halalytics menggunakan model kecerdasan buatan (Gemini AI) untuk menganalisis komposisi produk, bahan kritis halal, dan kalkulator kesehatan. Seluruh informasi bersifat edukatif dan referensial. Aplikasi ini <strong>tidak menggantikan</strong> diagnosis medis dari dokter profesional, resep obat klinis resmi, atau fatwa hukum kehalalan mutlak dari lembaga sertifikasi yang berwenang (seperti BPJPH/MUI). Gunakan asisten ini dengan bijak.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Copyright Footer -->
            <div class="border-t border-[#0d3b34]/40 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Halalytics' }}. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <a href="{{ route('privacy') }}" class="hover:text-gray-400 transition-colors">Privacy Policy</a>
                    <span>•</span>
                    <a href="{{ route('about') }}" class="hover:text-gray-400 transition-colors">Tentang Kami</a>
                    <span>•</span>
                    <span class="text-emerald-500/80">Made with 💚 for Healthy Life</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-[30px] left-[30px] z-[1999] w-12 h-12 rounded-xl bg-slate-900/80 hover:bg-emerald-600 text-white flex items-center justify-center shadow-lg transition-all duration-300 opacity-0 translate-y-4 pointer-events-none" aria-label="Back to top">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });

        // Back to top behavior
        const btt = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btt.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
                btt.classList.add('opacity-100', 'translate-y-0');
            } else {
                btt.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                btt.classList.remove('opacity-100', 'translate-y-0');
            }
        });
        btt.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    @hasSection('schema')
    <script type="application/ld+json">
{!! trim($__env->yieldContent('schema')) !!}
    </script>
    @endif
    <!-- Swiper.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
