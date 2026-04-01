<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName = $settings['site_name'] ?? 'HalalScan AI';
        $defaultDescription = $settings['site_description'] ?? 'AI-Powered Halal & Health Scanner App';
        $metaDescription = trim((string) $__env->yieldContent('description', $defaultDescription));
        $metaKeywords = trim((string) $__env->yieldContent('keywords', 'halal scanner, cek halal, interaksi obat, health score, BPOM'));
        $canonicalUrl = trim((string) $__env->yieldContent('canonical', url()->current()));
        $defaultOgImage = asset('images/logo.png');
        $ogImage = trim((string) $__env->yieldContent('og_image', $defaultOgImage));
        $pageTitle = trim((string) $__env->yieldContent('title', $siteName));
    @endphp

    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonicalUrl }}">
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
            --promo-green: #0ea56b;
            --promo-green-deep: #08734a;
            --promo-blue: #1f4fd6;
            --promo-ink: #0f172a;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, .font-brand { font-family: 'Space Grotesk', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #065f46 0%, #1d4ed8 100%); }
        .gradient-text { background: linear-gradient(135deg, #10b981, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
    </style>
    @stack('head')
    @yield('styles')
</head>
<body class="bg-white text-gray-800">

    <!-- ===== NAVBAR ===== -->
    <nav class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">H</span>
                    </div>
                    <span class="font-brand font-bold text-xl text-gray-900">
                        {{ $settings['site_name'] ?? 'HalalScan AI' }}
                    </span>
                </a>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-green-600 font-medium transition-colors {{ request()->routeIs('home') ? 'text-green-600' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('features') }}" class="text-gray-600 hover:text-green-600 font-medium transition-colors {{ request()->routeIs('features') ? 'text-green-600' : '' }}">
                        Fitur
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition-colors {{ request()->routeIs('blog*') ? 'text-green-600' : '' }}">
                        Blog
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-green-600 font-medium transition-colors {{ request()->routeIs('about') ? 'text-green-600' : '' }}">
                        Tentang
                    </a>
                    <a href="{{ route('download') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-full transition-colors">
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
                <a href="{{ route('home') }}" class="block py-2 px-4 text-gray-600 hover:bg-gray-50 rounded-lg">Beranda</a>
                <a href="{{ route('features') }}" class="block py-2 px-4 text-gray-600 hover:bg-gray-50 rounded-lg">Fitur</a>
                <a href="{{ route('blog.index') }}" class="block py-2 px-4 text-gray-600 hover:bg-gray-50 rounded-lg">Blog</a>
                <a href="{{ route('about') }}" class="block py-2 px-4 text-gray-600 hover:bg-gray-50 rounded-lg">Tentang</a>
                <a href="{{ route('download') }}" class="block py-2 px-4 bg-green-600 text-white rounded-lg font-semibold text-center">Download APK</a>
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

        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-900 text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">H</span>
                        </div>
                        <span class="font-bold text-xl">{{ $settings['site_name'] ?? 'HalalScan AI' }}</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        {{ $settings['site_description'] ?? 'AI-powered halal & health product intelligence platform.' }}
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-semibold mb-4 text-gray-300">Navigasi</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-green-400 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('features') }}" class="hover:text-green-400 transition-colors">Fitur</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-green-400 transition-colors">Blog</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-green-400 transition-colors">Tentang</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-green-400 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Download -->
                <div>
                    <h4 class="font-semibold mb-4 text-gray-300">Download</h4>
                    <a href="{{ $settings['playstore_url'] ?? '#' }}" target="_blank"
                       class="inline-flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
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
                <p>&copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'HalalScan AI' }}. All rights reserved.</p>
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
