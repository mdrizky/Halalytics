@extends('promo.layout')
@section('title', ($settings['site_name'] ?? 'Halalytics') . ' - Solusi Kesehatan & Halal Terpadu')

@section('styles')
<style>
    :root {
        --brand-primary: #004D40;
        --brand-secondary: #00C853;
        --brand-surface: #F8FCFB;
        --brand-text: #1A302D;
        --brand-accent: #26A69A;
    }

    body {
        background-color: var(--brand-surface);
        color: var(--brand-text);
        scroll-behavior: smooth;
    }

    /* Typography & Spacing Fixes */
    .section-title {
        font-size: 2.5rem;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: var(--brand-primary);
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: #64748b;
        max-width: 700px;
        margin-bottom: 3rem;
    }

    /* Premium Card Styles */
    .pro-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 30px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .pro-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 77, 64, 0.15);
        border-color: var(--brand-secondary);
    }

    /* Health Tool Interactive Styles */
    .tool-input-group {
        margin-bottom: 20px;
    }
    .tool-label {
        display: block;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .tool-input {
        width: 100%;
        background: #f1f5f9;
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 700;
        transition: all 0.2s;
    }
    .tool-input:focus {
        background: #fff;
        border-color: var(--brand-secondary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 200, 83, 0.1);
    }
    .tool-btn {
        width: 100%;
        background: var(--brand-primary);
        color: white;
        font-weight: 900;
        padding: 16px;
        border-radius: 14px;
        transition: all 0.3s;
        box-shadow: 0 10px 20px rgba(0, 77, 64, 0.2);
    }
    .tool-btn:hover {
        background: var(--brand-secondary);
        transform: scale(1.02);
    }

    /* Alphabet Filter Fix */
    .alphabet-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }
    .char-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: white;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .char-btn:hover, .char-btn.active {
        background: var(--brand-primary);
        color: white;
        border-color: var(--brand-primary);
    }

    /* Snellen Chart Simulator */
    .snellen-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
    }
    .snellen-char {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        letter-spacing: 20px;
        margin: 10px 0;
    }

    /* AI Assistant UI (Halalytics AI) */
    #ai-panel {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 60px -15px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        z-index: 2000;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .ai-header {
        background: var(--brand-primary);
        padding: 20px;
        color: white;
    }
    .ai-chat-box {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8fafc;
    }
    .ai-bubble-msg {
        background: white;
        padding: 12px 16px;
        border-radius: 16px 16px 16px 4px;
        margin-bottom: 12px;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        max-width: 85%;
    }
    .ai-bubble-msg.user {
        background: var(--brand-secondary);
        color: white;
        border: none;
        align-self: flex-end;
        border-radius: 16px 16px 4px 16px;
        margin-left: auto;
    }

    /* Hero Section Fix */
    .hero-container {
        padding-top: 140px;
        padding-bottom: 100px;
        background: radial-gradient(circle at 80% 20%, rgba(0, 200, 83, 0.1), transparent 40%);
    }
</style>
@endsection

@section('content')
<!-- ===== HERO SECTION ===== -->
<div class="hero-container px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-bold border border-emerald-100 mb-8">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Halalytics AI v2.0 - Kini Lebih Cerdas
            </div>
            <h1 class="text-6xl font-black text-gray-900 leading-tight mb-8">
                Pantau Kesehatan <br> Dengan <span class="text-emerald-600">Kepastian Halal.</span>
            </h1>
            <p class="text-xl text-gray-500 mb-10 leading-relaxed">
                Platform kesehatan terintegrasi yang menggabungkan kecerdasan AI dengan basis data halal terverifikasi. Konsultasi, cek nutrisi, dan skrining risiko dalam satu genggaman.
            </p>
            <div class="flex gap-4">
                <a href="{{ route('download') }}" class="bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black text-lg hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/30">
                    Download Aplikasi
                </a>
                <a href="#specialized" class="bg-white text-gray-700 border border-gray-200 px-8 py-4 rounded-2xl font-black text-lg hover:bg-gray-50 transition-all">
                    Layanan Khusus
                </a>
            </div>
        </div>
        <div class="hidden lg:flex justify-end relative">
            <div class="device-shell w-[300px] h-[600px] rotate-3 shadow-2xl">
                <img src="{{ asset('images/promo/ss-home-1.png') }}" alt="App Home" class="w-full h-full object-cover">
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-3xl shadow-2xl border border-emerald-50 w-64 z-10">
                <p class="text-xs font-black text-gray-400 uppercase mb-2 tracking-widest">Live Status</p>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <p class="font-bold text-gray-800">512 Dokter Online</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== LAYANAN UNGGULAN (Refined) ===== -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center">
            <h2 class="section-title">Layanan Unggulan Kami</h2>
            <p class="section-subtitle mx-auto">Kami menghadirkan ekosistem kesehatan digital terlengkap yang dirancang khusus untuk kebutuhan gaya hidup halal Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            @php
            $mainServices = [
                ['icon' => '👨‍⚕️', 'name' => 'Chat Dokter', 'desc' => 'Tanya pakar 24 jam', 'color' => '#f0fdf4', 'link' => route('download')],
                ['icon' => '🏠', 'name' => 'Homecare & Lab', 'desc' => 'Tes medis di rumah', 'color' => '#eff6ff', 'link' => route('download')],
                ['icon' => '📅', 'name' => 'Buat Janji RS', 'desc' => 'Pesan jadwal offline', 'color' => '#fff7ed', 'link' => route('download')],
                ['icon' => '🧠', 'name' => 'Kesehatan Mental', 'desc' => 'Konseling psikolog', 'color' => '#faf5ff', 'link' => route('specialized.show', 'mental')],
                ['icon' => '🛡️', 'name' => 'Klaim Asuransi', 'desc' => 'Integrasi cashless', 'color' => '#fef2f2', 'link' => route('download')]
            ];
            @endphp

            @foreach($mainServices as $s)
            <div class="pro-card group cursor-pointer" onclick="location.href='{{ $s['link'] }}'">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mb-6 transition-transform group-hover:scale-110" style="background: {{ $s['color'] }}">
                    {{ $s['icon'] }}
                </div>
                <h3 class="font-black text-gray-900 text-lg mb-2">{{ $s['name'] }}</h3>
                <p class="text-sm text-gray-500">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- BELI OBAT & SUPLEMEN (Halodoc Style) -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Beli Obat & Suplemen</h2>
                <p class="section-subtitle">Dapatkan obat-obatan dan vitamin terverifikasi halal dengan pengiriman cepat.</p>
            </div>
            <a href="#" class="text-emerald-700 font-black hover:underline mb-8">Lihat Semua Kategori</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($medicines ?? [] as $m)
            <div class="pro-card p-4 group cursor-pointer" onclick="location.href='{{ route('medicine.show', $m->id_medicine) }}'">
                <div class="aspect-square bg-gray-50 rounded-2xl mb-4 overflow-hidden flex items-center justify-center p-4">
                    @if($m->image_url)
                    <img src="{{ $m->image_url }}" alt="{{ $m->name }}" class="max-h-full transition-transform group-hover:scale-110">
                    @else
                    <span class="text-4xl">💊</span>
                    @endif
                </div>
                <h4 class="font-bold text-sm text-gray-900 line-clamp-2 min-h-[40px] mb-2">{{ $m->name }}</h4>
                <p class="text-emerald-600 font-black text-sm">Rp {{ number_format($m->price ?? 15000, 0, ',', '.') }}</p>
                <button class="w-full mt-4 py-2 border border-emerald-600 text-emerald-600 rounded-lg text-xs font-black group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    Cek Detail
                </button>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== PERAWATAN KHUSUS (New Section) ===== -->
<section id="specialized" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Perawatan Khusus</h2>
                <p class="text-gray-500 font-bold">Program kesehatan spesifik untuk penyakit kronis & lifestyle.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="pro-card border-l-8 border-l-emerald-600 cursor-pointer hover:bg-emerald-50" onclick="location.href='{{ route('specialized.show', 'diabetes') }}'">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Diabetes Care</h3>
                <p class="text-gray-600 mb-6">Program manajemen gula darah terpadu dengan pemantauan AI dan konsultasi spesialis endokrin.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Alat Monitor Gula Darah</li>
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Konsultasi Spesialis</li>
                </ul>
            </div>

            <div class="pro-card border-l-8 border-l-rose-500 cursor-pointer hover:bg-rose-50" onclick="location.href='{{ route('specialized.show', 'heart') }}'">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Heart Health</h3>
                <p class="text-gray-600 mb-6">Pemantauan tekanan darah, tips diet rendah kolesterol, dan deteksi dini risiko kardiovaskular.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Cek Kolesterol Homecare</li>
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Diet Plan Jantung</li>
                </ul>
            </div>

            <div class="pro-card border-l-8 border-l-purple-600 cursor-pointer hover:bg-purple-50" onclick="location.href='{{ route('specialized.show', 'mental') }}'">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Mental Health Center</h3>
                <p class="text-gray-600 mb-6">Sesi konseling mendalam dengan psikolog klinis untuk depresi, kecemasan, dan manajemen stres.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Skrining Mental Gratis</li>
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Sesi Terapi Privat</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== CEK KESEHATAN MANDIRI (Expanded) ===== -->
<section id="health-tools" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="section-title">Cek Kesehatan Mandiri</h2>
            <p class="section-subtitle mx-auto">Gunakan kalkulator medis kami yang divalidasi oleh tenaga profesional untuk memantau kondisi tubuh Anda secara instan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- 1. Nutrisi & Kalori -->
            <div class="pro-card shadow-xl border-emerald-50">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">🥗</span> Kalkulator Nutrisi & BMR
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="tool-input-group">
                        <label class="tool-label">Usia</label>
                        <input type="number" id="nut_age" placeholder="Contoh: 25" class="tool-input">
                    </div>
                    <div class="tool-input-group">
                        <label class="tool-label">Jenis Kelamin</label>
                        <select id="nut_gender" class="tool-input">
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div class="tool-input-group">
                        <label class="tool-label">Berat Badan (kg)</label>
                        <input type="number" id="nut_weight" placeholder="Contoh: 70" class="tool-input">
                    </div>
                    <div class="tool-input-group">
                        <label class="tool-label">Tinggi (cm)</label>
                        <input type="number" id="nut_height" placeholder="Contoh: 175" class="tool-input">
                    </div>
                </div>
                <div class="tool-input-group">
                    <label class="tool-label">Tingkat Aktivitas</label>
                    <select id="nut_activity" class="tool-input">
                        <option value="1.2">Jarang Bergerak (Sedentary)</option>
                        <option value="1.375">Aktivitas Ringan (1-3 hari/minggu)</option>
                        <option value="1.55">Aktivitas Sedang (3-5 hari/minggu)</option>
                        <option value="1.725">Sangat Aktif (6-7 hari/minggu)</option>
                    </select>
                </div>
                <button onclick="calculateBMR()" class="tool-btn">Hitung Kebutuhan Kalori</button>
                <div id="bmr_res" class="hidden mt-6 p-6 bg-emerald-50 rounded-2xl text-center">
                    <p class="text-sm font-bold text-emerald-800">Kebutuhan Kalori Harian Anda:</p>
                    <p id="bmr_val" class="text-4xl font-black text-emerald-600 mt-1">2,450 kcal</p>
                </div>
            </div>

            <!-- 2. Target Air Minum -->
            <div class="pro-card shadow-xl border-blue-50">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">💧</span> Target Air Minum
                </h3>
                <div class="tool-input-group">
                    <label class="tool-label">Berat Badan (kg)</label>
                    <input type="number" id="water_weight" placeholder="Contoh: 60" class="tool-input">
                </div>
                <div class="tool-input-group">
                    <label class="tool-label">Lingkungan / Cuaca</label>
                    <select id="water_weather" class="tool-input">
                        <option value="normal">Normal / Dingin</option>
                        <option value="hot">Cuaca Panas / Berkeringat</option>
                    </select>
                </div>
                <button onclick="calculateWater()" class="tool-btn bg-blue-600 hover:bg-blue-700">Cek Target Air</button>
                <div id="water_res" class="hidden mt-6 p-6 bg-blue-50 rounded-2xl text-center">
                    <p class="text-sm font-bold text-blue-800">Target Air Harian Anda:</p>
                    <p id="water_val" class="text-4xl font-black text-blue-600 mt-1">2.5 Liter</p>
                </div>
            </div>

            <!-- 3. Skrining Risiko (Lifestyle) -->
            <div class="pro-card shadow-xl border-rose-50 lg:col-span-1">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">🫀</span> Skrining Risiko Lifestyle
                </h3>
                <div class="space-y-4 mb-6">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-bold">Apakah Anda merokok?</span>
                        <input type="checkbox" id="risk_smoke" class="w-5 h-5 accent-rose-500">
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-bold">Ada riwayat penyakit keluarga?</span>
                        <input type="checkbox" id="risk_family" class="w-5 h-5 accent-rose-500">
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-bold">Tidur kurang dari 6 jam/hari?</span>
                        <input type="checkbox" id="risk_sleep" class="w-5 h-5 accent-rose-500">
                    </div>
                </div>
                <button onclick="calculateRisk()" class="tool-btn bg-rose-600 hover:bg-rose-700">Cek Skor Risiko</button>
                <div id="risk_res" class="hidden mt-6 p-6 rounded-2xl text-center">
                    <p class="text-sm font-bold opacity-80">Skor Risiko Anda:</p>
                    <p id="risk_val" class="text-2xl font-black mt-1">RENDAH</p>
                </div>
            </div>

            <!-- 4. Kehamilan & Masa Subur -->
            <div class="pro-card shadow-xl border-amber-50">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">👶</span> Kalkulator Kehamilan
                </h3>
                <div class="tool-input-group">
                    <label class="tool-label">Hari Pertama Haid Terakhir (HPHT)</label>
                    <input type="date" id="hpht_date" class="tool-input">
                </div>
                <button onclick="calculateDueDate()" class="tool-btn bg-amber-600 hover:bg-amber-700">Estimasi Kelahiran</button>
                <div id="due_res" class="hidden mt-6 p-6 bg-amber-50 rounded-2xl text-center">
                    <p class="text-sm font-bold text-amber-800">Estimasi Tanggal Persalinan:</p>
                    <p id="due_val" class="text-2xl font-black text-amber-700 mt-1">12 Desember 2026</p>
                </div>
            </div>

            <!-- 5. Tes Ketajaman Mata -->
            <div class="pro-card shadow-xl border-gray-100 lg:col-span-2">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">👁️</span> Tes Ketajaman Mata (Snellen)
                </h3>
                <p class="text-sm text-gray-500 mb-6">Berdiri 1 meter dari layar dan coba baca huruf di bawah ini satu per satu dengan satu mata tertutup.</p>
                <div class="snellen-box">
                    <div class="snellen-char text-6xl">E</div>
                    <div class="snellen-char text-4xl">F P</div>
                    <div class="snellen-char text-2xl">T O Z</div>
                    <div class="snellen-char text-xl">L P E D</div>
                </div>
                <div class="mt-8 text-center">
                    <button class="px-8 py-3 bg-gray-800 text-white font-black rounded-xl" onclick="alert('Tes selesai! Jika Anda kesulitan membaca baris terbawah, segera konsultasikan ke dokter mata.')">Selesai Tes</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== KAMUS KESEHATAN A-Z (Valid Data) ===== -->
<section id="kamus" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="section-title">Kamus Kesehatan A-Z</h2>
            <p class="section-subtitle mx-auto">Database penyakit, gejala, dan obat-obatan terlengkap yang disusun secara alfabetis oleh tim medis Halalytics.</p>
        </div>

        <div class="alphabet-grid mb-16">
            @foreach(range('A', 'Z') as $c)
            <div class="char-btn @if($c == 'A') active @endif">{{ $c }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $diseases = [
                ['name' => 'Abdominal Migrain', 'desc' => 'Nyeri perut parah yang sering terjadi pada anak-anak.'],
                ['name' => 'Abses Gigi', 'desc' => 'Kumpulan nanah di gigi atau gusi akibat infeksi bakteri.'],
                ['name' => 'Acne Vulgaris', 'desc' => 'Masalah kulit berupa jerawat akibat penyumbatan pori.'],
                ['name' => 'Anemia', 'desc' => 'Kondisi kekurangan sel darah merah yang sehat dalam tubuh.'],
                ['name' => 'Asma', 'desc' => 'Penyempitan saluran pernapasan akibat peradangan kronis.'],
                ['name' => 'Alergi Makanan', 'desc' => 'Reaksi sistem imun terhadap protein tertentu dalam makanan.']
            ];
            @endphp
            @foreach($diseases as $d)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-emerald-200 transition-all cursor-pointer">
                <h4 class="font-black text-gray-900 mb-2">{{ $d['name'] }}</h4>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $d['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ===== HALALYTICS AI (HILDA) ===== -->
<style>
    .ai-fab { position: fixed; bottom: 30px; right: 30px; z-index: 1999; display: flex; align-items: center; gap: 12px; }
    .ai-fab-label { background: white; color: #004D40; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 13px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
    .ai-fab-btn { width: 60px; height: 60px; border-radius: 20px; background: linear-gradient(135deg, #004D40, #00C853); color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; box-shadow: 0 8px 30px rgba(0,77,64,.4); transition: transform .2s; }
    .ai-fab-btn:hover { transform: scale(1.1); }
    .ai-chip { display: inline-block; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s; margin: 3px; }
    .ai-chip:hover { background: #166534; color: white; }
    .ai-typing span { width: 8px; height: 8px; background: #94a3b8; border-radius: 50%; display: inline-block; animation: typing .8s infinite; margin: 0 2px; }
    .ai-typing span:nth-child(2) { animation-delay: .15s; }
    .ai-typing span:nth-child(3) { animation-delay: .3s; }
    @keyframes typing { 0%,100% { opacity:.3; } 50% { opacity:1; } }
</style>

<div class="ai-fab" id="ai-fab">
    <div class="ai-fab-label">Tanya HILDA AI</div>
    <div class="ai-fab-btn" onclick="toggleAI()">🤖</div>
</div>

<div id="ai-panel">
    <div class="ai-header flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg">🤖</div>
            <div>
                <p class="font-black text-sm">Halalytics AI (HILDA)</p>
                <p class="text-[10px] text-emerald-300">Online</p>
            </div>
        </div>
        <button onclick="toggleAI()" class="text-white opacity-50 hover:opacity-100 text-xl">&times;</button>
    </div>
    <div class="ai-chat-box" id="chat-messages">
        <div class="ai-bubble-msg">Halo! Saya <b>HILDA</b> 🤖 Tanyakan tentang penyakit, obat, nutrisi, atau kehalalan produk!</div>
        <div style="padding:8px 0">
            <span class="ai-chip" onclick="askChip('Gejala diabetes')">Gejala Diabetes</span>
            <span class="ai-chip" onclick="askChip('Vitamin untuk imun')">Vitamin Imun</span>
            <span class="ai-chip" onclick="askChip('Cek halal')">Cek Halal</span>
        </div>
    </div>
    <div class="p-3 bg-white border-t border-gray-100 flex gap-2">
        <input type="text" id="ai-input" placeholder="Tulis pertanyaan..." class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none" onkeypress="if(event.key==='Enter')sendAIMessage()">
        <button onclick="sendAIMessage()" class="bg-emerald-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-lg hover:bg-emerald-700 flex-shrink-0">&rarr;</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
function calculateBMR(){const a=document.getElementById('nut_age').value,w=document.getElementById('nut_weight').value,h=document.getElementById('nut_height').value,g=document.getElementById('nut_gender').value,ac=document.getElementById('nut_activity').value;if(!a||!w||!h){alert('Harap lengkapi semua data!');return}let b=g==='male'?88.362+(13.397*w)+(4.799*h)-(5.677*a):447.593+(9.247*w)+(3.098*h)-(4.330*a);document.getElementById('bmr_val').innerText=Math.round(b*ac).toLocaleString()+' kcal';document.getElementById('bmr_res').classList.remove('hidden')}
function calculateWater(){const w=document.getElementById('water_weight').value;if(!w){alert('Masukkan berat badan!');return}let l=(w*0.033).toFixed(1);if(document.getElementById('water_weather').value==='hot')l=(parseFloat(l)+0.8).toFixed(1);document.getElementById('water_val').innerText=l+' Liter';document.getElementById('water_res').classList.remove('hidden')}
function calculateRisk(){let s=0;if(document.getElementById('risk_smoke').checked)s+=40;if(document.getElementById('risk_family').checked)s+=30;if(document.getElementById('risk_sleep').checked)s+=20;const b=document.getElementById('risk_res'),v=document.getElementById('risk_val');b.classList.remove('hidden');if(s>=60){v.innerText='TINGGI';b.className='mt-6 p-6 rounded-2xl text-center bg-rose-50 text-rose-600'}else if(s>=30){v.innerText='SEDANG';b.className='mt-6 p-6 rounded-2xl text-center bg-amber-50 text-amber-600'}else{v.innerText='RENDAH';b.className='mt-6 p-6 rounded-2xl text-center bg-emerald-50 text-emerald-600'}}
function calculateDueDate(){const h=document.getElementById('hpht_date').value;if(!h){alert('Masukkan tanggal HPHT!');return}const d=new Date(h);d.setDate(d.getDate()+7);d.setMonth(d.getMonth()-3);d.setFullYear(d.getFullYear()+1);document.getElementById('due_val').innerText=d.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});document.getElementById('due_res').classList.remove('hidden')}

// HILDA AI
function toggleAI(){const p=document.getElementById('ai-panel'),f=document.getElementById('ai-fab');const o=p.style.display==='flex';p.style.display=o?'none':'flex';f.style.display=o?'flex':'none'}
function askChip(t){document.getElementById('ai-input').value=t;sendAIMessage()}

const hildaKB=[
{keys:['diabetes','gula darah','insulin','kencing manis'],answer:'🩺 <b>Diabetes Mellitus</b><br><br><b>Gejala umum:</b><br>• Sering buang air kecil<br>• Haus berlebihan<br>• Penurunan BB tanpa sebab<br>• Luka sulit sembuh<br><br><b>Gula darah normal:</b> Puasa 70-100 mg/dL<br><br>💡 Download Halalytics untuk program Diabetes Care!'},
{keys:['kolesterol','ldl','hdl'],answer:'🫀 <b>Kolesterol</b><br><br><b>Nilai normal:</b><br>• Total: < 200 mg/dL<br>• LDL: < 100 mg/dL<br>• HDL: > 40 mg/dL<br><br><b>Tips:</b> Konsumsi oat, ikan salmon, olahraga 30 mnt/hari'},
{keys:['jantung','serangan jantung','tekanan darah','hipertensi','darah tinggi'],answer:'❤️ <b>Kesehatan Jantung</b><br><br><b>TD normal:</b> < 120/80 mmHg<br><br><b>Tanda bahaya:</b><br>• Nyeri dada tertekan<br>• Nyeri ke lengan kiri<br>• Sesak napas & keringat dingin<br><br>⚠️ Jika mengalami ini, SEGERA hubungi 119!'},
{keys:['depresi','cemas','anxiety','stres','mental','psikolog'],answer:'🧠 <b>Kesehatan Mental</b><br><br><b>Perlu bantuan jika:</b><br>• Sedih > 2 minggu<br>• Kehilangan minat<br>• Gangguan tidur<br>• Sulit konsentrasi<br><br>Hotline: 119 ext 8<br>💚 Minta bantuan itu tanda kekuatan.'},
{keys:['vitamin','suplemen','daya tahan','imun'],answer:'💊 <b>Vitamin untuk Imunitas</b><br><br>• <b>Vitamin C</b> 500-1000mg/hari<br>• <b>Vitamin D3</b> 1000 IU/hari<br>• <b>Zinc</b> 15mg/hari<br>• <b>Probiotik</b> untuk usus sehat<br><br>✅ Semua vitamin di Halalytics terverifikasi halal!'},
{keys:['halal','haram','babi','gelatin','cek halal'],answer:'✅ <b>Cek Kehalalan Produk</b><br><br>Halalytics membantu via:<br>• Scan barcode instan<br>• Analisis bahan AI<br>• Database BPOM & MUI<br><br><b>Waspadai:</b> Gelatin, E120, Alkohol, Shortening<br><br>📱 Download Halalytics untuk scan!'},
{keys:['maag','asam lambung','gerd','lambung'],answer:'🏥 <b>GERD/Maag</b><br><br>• Makan teratur, porsi kecil<br>• Hindari pedas, asam, kafein<br>• Jangan tiduran setelah makan<br>• Obat: Antasida, Omeprazole'},
{keys:['flu','batuk','pilek','demam'],answer:'🤒 <b>Flu & Demam</b><br><br>• Istirahat cukup<br>• Minum 2-3 liter/hari<br>• Paracetamol untuk demam > 38°C<br>• Madu + lemon untuk batuk<br><br>Ke dokter jika demam > 3 hari'},
{keys:['diet','kalori','berat badan','obesitas'],answer:'🥗 <b>Manajemen BB</b><br><br>BMI ideal: 18.5-24.9<br>• Defisit 300-500 kcal/hari<br>• Protein 1.2-1.6 g/kg<br>• Sayur & buah 5 porsi/hari<br><br>🧮 Gunakan Kalkulator BMR di atas!'},
{keys:['hamil','kehamilan','ibu hamil','janin'],answer:'🤰 <b>Kehamilan</b><br><br><b>Nutrisi penting:</b><br>• Asam Folat 400-800 mcg<br>• Zat Besi 27 mg/hari<br>• Kalsium 1000 mg/hari<br>• DHA/Omega-3<br><br>👶 Gunakan Kalkulator Kehamilan di atas!'},
{keys:['kulit','jerawat','skincare','acne'],answer:'✨ <b>Perawatan Kulit</b><br><br>Pagi: Cleanser → Moisturizer → SPF<br>Malam: Cleanser → Serum → Moisturizer<br><br>Jerawat: Salicylic Acid, Niacinamide<br>💡 Coba Haloskin untuk analisis AI!'},
];

function getHildaResponse(t){const l=t.toLowerCase();for(const e of hildaKB){if(e.keys.some(k=>l.includes(k)))return e.answer}if(l.includes('halo')||l.includes('hai')||l.includes('hi'))return'Halo! 👋 Saya HILDA, asisten kesehatan Anda. Tanyakan tentang penyakit, obat, nutrisi, atau kehalalan produk!';if(l.includes('terima kasih')||l.includes('makasih'))return'Sama-sama! 😊 Senang bisa membantu!';if(l.includes('obat'))return'💊 Cek bagian Beli Obat & Suplemen di atas, atau scan barcode di aplikasi Halalytics!';return'🤔 Saya belum punya info spesifik tentang itu. Coba tanyakan tentang: diabetes, kolesterol, kesehatan mental, vitamin, kehalalan, diet, atau kehamilan!'}

function sendAIMessage(){const i=document.getElementById('ai-input'),c=document.getElementById('chat-messages'),t=i.value.trim();if(!t)return;const u=document.createElement('div');u.className='ai-bubble-msg user';u.innerText=t;c.appendChild(u);i.value='';c.scrollTop=c.scrollHeight;const tp=document.createElement('div');tp.innerHTML='<span></span><span></span><span></span>';tp.className='ai-typing';c.appendChild(tp);c.scrollTop=c.scrollHeight;setTimeout(()=>{tp.remove();const b=document.createElement('div');b.className='ai-bubble-msg';b.innerHTML=getHildaResponse(t);c.appendChild(b);c.scrollTop=c.scrollHeight},1200)}
</script>
@endsection
