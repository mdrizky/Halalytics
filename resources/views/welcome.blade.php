<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halalytics | Super App Pintar Verifikasi Halal & Kesehatan</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#059669',
                        'primary-light': '#10B981',
                        accent: '#F4A261',
                    }
                }
            }
        }
    </script>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_halalytics.png') }}">
    
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(16, 185, 129, 0.1) 0%, transparent 40%),
                        radial-gradient(circle at bottom left, rgba(5, 150, 105, 0.05) 0%, transparent 40%);
        }
        .feature-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.08);
            border-color: #10B981;
        }
        .text-gradient {
            background: linear-gradient(135deg, #059669, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .scan-line {
            animation: scan 3s infinite linear;
        }
        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
    </style>
</head>
<body class="bg-gray-50 text-slate-800 antialiased overflow-x-hidden">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="bg-white p-1.5 rounded-xl shadow-sm border border-gray-100">
                        <img src="{{ asset('images/logo_halalytics.png') }}?v={{ time() }}" alt="Halalytics Logo" class="h-8 w-auto object-contain">
                    </div>
                    <span class="font-extrabold text-xl text-primary tracking-tight">Halalytics</span>
                </div>
                
                <!-- Links (Desktop) -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#features" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors">Fitur</a>
                    <a href="#ecosystem" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors">Ekosistem</a>
                    <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-slate-600 hover:text-primary transition-colors">Blog</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/admin') }}" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-sm shadow-lg shadow-primary/30 hover:-translate-y-0.5 transition-transform">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl border-2 border-primary text-primary font-bold text-sm hover:bg-primary/5 transition-colors">
                            Admin Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 hero-gradient min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col lg:flex-row items-center gap-12">
            
            <!-- Text Content -->
            <div class="lg:w-1/2 text-center lg:text-left z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary font-bold text-xs tracking-wider mb-6">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    SUPER APP HALAL & KESEHATAN
                </div>
                <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Masa Depan <br/> <span class="text-gradient">Gaya Hidup Halal</span> & Sehat Anda.
                </h1>
                <p class="text-lg text-slate-500 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Scan bahan makanan dengan AI, cek skor kesehatan, konsultasi dengan pakar gizi, hingga donasi darah dalam satu Super App cerdas. Terintegrasi dengan BPOM dan LPPOM MUI.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#download" class="px-8 py-4 rounded-2xl bg-primary text-white font-bold shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                        <i class="fab fa-google-play text-xl"></i>
                        Download App
                    </a>
                    <a href="#demo" class="px-8 py-4 rounded-2xl bg-white text-slate-700 font-bold border border-gray-200 hover:border-primary/50 hover:bg-gray-50 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-play-circle text-primary text-xl"></i>
                        Lihat Demo
                    </a>
                </div>
            </div>

            <!-- Hero Mockup -->
            <div class="lg:w-1/2 relative flex justify-center mt-12 lg:mt-0">
                <!-- Floating Badges -->
                <div class="absolute top-10 -left-10 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 z-20 animate-float" style="animation-delay: 0s;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold">Skor Kesehatan</p>
                            <p class="text-sm font-extrabold text-slate-800">85/100 (SEHAT)</p>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-20 -right-10 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 z-20 animate-float" style="animation-delay: 2s;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold">Peringatan Bahan</p>
                            <p class="text-sm font-extrabold text-slate-800">E-120 Karmin Terdeteksi</p>
                        </div>
                    </div>
                </div>

                <!-- Phone Mockup -->
                <div class="relative w-[300px] h-[600px] bg-slate-900 rounded-[3rem] border-[8px] border-slate-800 shadow-2xl overflow-hidden z-10">
                    <div class="absolute top-0 inset-x-0 h-6 bg-slate-800 rounded-b-3xl w-1/2 mx-auto z-30"></div>
                    <!-- App UI Replica -->
                    <div class="bg-gray-50 w-full h-full flex flex-col p-5 pt-12 relative">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-primary">Scan Produk</h3>
                            <div class="w-8 h-8 rounded-full bg-slate-200"></div>
                        </div>
                        
                        <div class="relative w-full h-48 bg-gray-200 rounded-2xl overflow-hidden mb-6 shadow-inner">
                            <img src="https://images.unsplash.com/photo-1621939514649-280e2ee25f60?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover">
                            <!-- Scanning effect -->
                            <div class="absolute w-full h-1 bg-primary/80 shadow-[0_0_15px_rgba(16,185,129,0.8)] scan-line z-10"></div>
                        </div>

                        <p class="font-extrabold text-sm mb-2 text-slate-800">Menganalisis Komposisi...</p>
                        <div class="w-full h-2 bg-gray-200 rounded-full mb-6">
                            <div class="w-3/4 h-full bg-primary rounded-full relative overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 -translate-x-full animate-[shimmer_1.5s_infinite]"></div>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mt-auto">
                            <div class="w-full h-16 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center p-3 gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-primary"><i class="fas fa-leaf"></i></div>
                                <div class="flex-1">
                                    <div class="h-3 w-20 bg-gray-200 rounded-full mb-2"></div>
                                    <div class="h-2 w-32 bg-gray-100 rounded-full"></div>
                                </div>
                            </div>
                            <div class="w-full h-16 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center p-3 gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500"><i class="fas fa-heartbeat"></i></div>
                                <div class="flex-1">
                                    <div class="h-3 w-16 bg-gray-200 rounded-full mb-2"></div>
                                    <div class="h-2 w-24 bg-gray-100 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-slate-900 text-white py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/20 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/20 via-slate-900 to-slate-900"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 flex flex-wrap justify-around items-center gap-8 text-center">
            <div>
                <h3 class="text-4xl lg:text-5xl font-extrabold text-white mb-2">1M+</h3>
                <p class="text-sm font-semibold text-primary-light uppercase tracking-widest">Produk Terverifikasi</p>
            </div>
            <div class="hidden md:block w-px h-16 bg-slate-700"></div>
            <div>
                <h3 class="text-4xl lg:text-5xl font-extrabold text-white mb-2">10M+</h3>
                <p class="text-sm font-semibold text-primary-light uppercase tracking-widest">Total Scan AI</p>
            </div>
            <div class="hidden md:block w-px h-16 bg-slate-700"></div>
            <div>
                <h3 class="text-4xl lg:text-5xl font-extrabold text-white mb-2">500+</h3>
                <p class="text-sm font-semibold text-primary-light uppercase tracking-widest">Pakar Kesehatan</p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-primary font-bold tracking-wider text-sm uppercase mb-3 block">Fitur Unggulan</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-4">Ekosistem Super App Lengkap</h2>
                <p class="text-slate-500 text-lg">Platform pertama di Indonesia yang menggabungkan verifikasi halal OCR, analisis kesehatan nutrisi, dan integrasi pakar medis.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-md text-2xl text-primary mb-6">
                        <i class="fas fa-expand"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">AI OCR Scanner</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Pindai label komposisi dengan kamera. AI kami (Llama-3 & Gemini) menganalisis E-Numbers, status syubhat, dan keamanan bahan secara real-time.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-md text-2xl text-blue-500 mb-6">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Health Score Calculator</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Hitung otomatis skor kesehatan produk (0-100) berdasarkan Nova Group, gula, lemak jenuh, dan garam. Dapatkan rekomendasi alternatif sehat.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-md text-2xl text-purple-500 mb-6">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Halocode (Konsultasi Pakar)</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Chat langsung dengan ahli gizi terverifikasi untuk perencanaan diet (Meal Plan) dan pengecekan rekam medis. Aman dan tersinkronisasi.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-md text-2xl text-red-500 mb-6">
                        <i class="fas fa-hand-holding-medical"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Donor Darah & Darurat</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Pusat donor darah interaktif. Minta donor darurat, daftar event donor terdekat, dan lacak riwayat donor dengan QR Code digital.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-md text-2xl text-amber-500 mb-6">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Gamifikasi & Komunitas</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Kumpulkan poin dari setiap scan dan aksi sehat Anda. Baca artikel kesehatan terbaru dan bagikan perjalanan diet Anda ke komunitas.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-md text-2xl text-emerald-600 mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">BPOM & LPPOM MUI Sync</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Validasi nomor registrasi sertifikat halal dan nomor registrasi BPOM seketika untuk menangkal produk ilegal atau kadaluarsa izin.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6">
        <div class="max-w-5xl mx-auto bg-primary rounded-[3rem] p-12 text-center text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-black/10 rounded-full blur-3xl"></div>
            
            <h2 class="text-3xl md:text-5xl font-extrabold mb-6 relative z-10">Mulai Gaya Hidup Sehat & Halal Anda Hari Ini</h2>
            <p class="text-primary-light text-lg mb-10 max-w-2xl mx-auto relative z-10">Aplikasi pendamping cerdas Anda untuk setiap makanan yang Anda beli dan konsumsi.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                <a href="#" class="px-8 py-4 rounded-xl bg-white text-primary font-bold hover:scale-105 transition-transform flex items-center justify-center gap-3">
                    <i class="fab fa-apple text-xl"></i> App Store
                </a>
                <a href="#" class="px-8 py-4 rounded-xl bg-slate-900 text-white font-bold hover:scale-105 transition-transform flex items-center justify-center gap-3">
                    <i class="fab fa-google-play text-xl"></i> Play Store
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-3 mb-6">
                <img src="{{ asset('images/logo_halalytics.png') }}?v={{ time() }}" alt="Logo" class="h-8 w-auto">
                <span class="font-extrabold text-lg text-slate-800">Halalytics Super App</span>
            </div>
            <p class="text-slate-500 text-sm mb-6">© {{ date('Y') }} Halalytics Ecosystem. All rights reserved.<br/>Built with Laravel 11 & Jetpack Compose.</p>
            <div class="flex justify-center gap-6 text-gray-400">
                <a href="#" class="hover:text-primary transition-colors"><i class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="hover:text-primary transition-colors"><i class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="hover:text-primary transition-colors"><i class="fab fa-github text-xl"></i></a>
            </div>
        </div>
    </footer>

    <script>
        // Navbar shadow on scroll
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 10) {
                nav.classList.add('shadow-sm');
                nav.classList.replace('h-20', 'h-16');
            } else {
                nav.classList.remove('shadow-sm');
                nav.classList.replace('h-16', 'h-20');
            }
        });
    </script>
</body>
</html>
