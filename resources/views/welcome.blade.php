<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halalytics | Intelligent Halal Verification Ecosystem</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_halalytics.png') }}">
    <style>
        :root {
            --primary: #059669;
            --primary-light: #10B981;
            --secondary: #FFFFFF;
            --accent: #F4A261;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --bg-light: #F8FAF9;
            --danger: #E74C3C;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Navbar */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 24px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            transition: 0.3s;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-weight: 600;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-actions {
            display: flex;
            gap: 16px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            box-shadow: 0 10px 20px rgba(45, 106, 79, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            padding: 0 80px;
            position: relative;
            background: radial-gradient(circle at 90% 10%, rgba(45, 106, 79, 0.05) 0%, transparent 40%);
        }

        .hero-content {
            max-width: 600px;
            z-index: 10;
        }

        .hero-tag {
            background: rgba(45, 106, 79, 0.1);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 24px;
            letter-spacing: 1px;
        }

        .hero h1 {
            font-size: 72px;
            line-height: 1.05;
            font-weight: 800;
            margin-bottom: 24px;
            color: #0F172A;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.04em;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .hero-image {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 50%;
            height: 80vh;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .mockup-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .mockup-phone {
            width: 320px;
            height: 650px;
            background: #111;
            border-radius: 40px;
            border: 8px solid #333;
            box-shadow: 0 50px 100px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            z-index: 5;
        }

        .mockup-content {
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            z-index: 10;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Features */
        .features {
            padding: 120px 80px;
            background: white;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 80px;
        }

        .section-header h2 {
            font-size: 40px;
            margin-bottom: 16px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .feature-card {
            padding: 40px;
            border-radius: 32px;
            background: var(--bg-light);
            transition: 0.4s;
            border: 1px solid transparent;
        }

        .feature-card:hover {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(45, 106, 79, 0.05);
            transform: translateY(-10px);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 32px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        /* Stats */
        .stats {
            padding: 80px;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .stat-item p {
            font-size: 14px;
            opacity: 0.8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* CTA */
        .cta {
            padding: 120px 80px;
            text-align: center;
            background: linear-gradient(135deg, #2D6A4F 0%, #1B4332 100%);
            color: white;
            margin: 80px;
            border-radius: 48px;
        }

        .cta h2 {
            font-size: 48px;
            margin-bottom: 24px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            nav { padding: 20px 40px; }
            .hero { padding: 0 40px; flex-direction: column; text-align: center; justify-content: center; }
            .hero-image { display: none; }
            .features-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <div style="background: white; border-radius: 8px; padding: 4px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="background: white; padding: 4px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                <img src="{{ asset('images/logo_halalytics.png') }}?v={{ time() }}" alt="Halalytics Logo" style="height: 36px; width: auto; object-fit: contain;">
            </div>
            </div>
            <span style="font-weight: 800; letter-spacing: -0.5px;">Halalytics</span>
        </div>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#how-it-works">How it Works</a></li>
            <li><a href="{{ route('blog.index') }}">Blog</a></li>
            <li><a href="#about">About</a></li>
        </ul>
        <div class="nav-actions">
            @auth
                <a href="{{ url('/admin') }}" class="btn btn-primary">Admin Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Admin Login</a>
            @endauth
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-tag">AI-POWERED HALAL INTELLIGENCE</div>
            <h1>The Future of <span>Halal Verification</span> is Here.</h1>
            <p>Scan, verifikasi, dan temukan produk halal dengan teknologi AI tercanggih. Database terintegrasi BPOM, LPPOM MUI, dan ribuan basis data internasional.</p>
            <div style="display: flex; gap: 16px;">
                <a href="#download" class="btn btn-primary" style="padding: 16px 32px; font-size: 16px;">
                    <i class="fab fa-google-play"></i> Download Now
                </a>
                <a href="#demo" class="btn btn-outline" style="padding: 16px 32px; font-size: 16px;">
                    Watch Demo
                </a>
            </div>
        </div>
        <div class="hero-image">
            <div class="mockup-container">
                <div class="floating-card" style="top: 15%; left: 10%; border-left: 4px solid var(--primary);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(45, 106, 79, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 14px;">100% Halal Verified</div>
                            <div style="font-size: 11px; color: var(--text-muted);">Sync with LPPOM MUI</div>
                        </div>
                    </div>
                </div>

                <div class="floating-card" style="bottom: 20%; right: 5%; border-left: 4px solid var(--danger);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(231, 76, 60, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 14px;">Forbidden Alert</div>
                            <div style="font-size: 11px; color: var(--text-muted);">Contains E-120 Carmine</div>
                        </div>
                    </div>
                </div>

                <div class="mockup-phone">
                    <div class="mockup-content">
                        <div style="height: 40px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 800; color: var(--primary);">Halalytics</div>
                            <i class="fas fa-user-circle" style="font-size: 24px; color: #ddd;"></i>
                        </div>
                        <div style="background: #f3f4f6; height: 200px; border-radius: 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                             <img src="https://images.unsplash.com/photo-1615485290382-441e4d019cb0?auto=format&fit=crop&q=80&w=800" style="width: 100%; height: 100%; object-fit: cover;">
                             <div style="position: absolute; width: 100%; height: 2px; background: rgba(45, 106, 79, 0.5); box-shadow: 0 0 10px var(--primary); animation: scanLine 3s infinite;"></div>
                        </div>
                        <style>
                            @keyframes scanLine {
                                0% { top: 0; }
                                50% { top: 100%; }
                                100% { top: 0; }
                            }
                        </style>
                        <div style="font-weight: 800; margin-bottom: 8px;">Analyzing Ingredients...</div>
                        <div style="height: 10px; background: #eee; border-radius: 5px; margin-bottom: 20px;">
                            <div style="width: 75%; height: 100%; background: var(--primary); border-radius: 5px;"></div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div style="height: 60px; background: #f8faf9; border-radius: 12px; border: 1px solid #eee;"></div>
                            <div style="height: 60px; background: #f8faf9; border-radius: 12px; border: 1px solid #eee;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item">
            <h3 id="stat-products">500k+</h3>
            <p>Verified Products</p>
        </div>
        <div class="stat-item">
            <h3 id="stat-users">1.2M+</h3>
            <p>Happy Users</p>
        </div>
        <div class="stat-item">
            <h3 id="stat-scans">10M+</h3>
            <p>Total Scans</p>
        </div>
    </section>

    <section id="features" class="features">
        <div class="section-header">
            <div class="hero-tag">OUR ECOSYSTEM</div>
            <h2>Semua yang Anda butuhkan untuk gaya hidup halal.</h2>
            <p>Platform terintegrasi yang menggabungkan kecerdasan buatan dengan data verifikasi otoritas resmi.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-qrcode"></i></div>
                <h3>AI OCR Scanner</h3>
                <p style="margin-top: 16px; color: var(--text-muted); line-height: 1.6;">Gunakan kamera ponsel untuk memindai komposisi bahan. AI kami akan mendeteksi titik kritis kehalalan dalam hitungan detik.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-ban"></i></div>
                <h3>Forbidden Database</h3>
                <p style="margin-top: 16px; color: var(--text-muted); line-height: 1.6;">Basis data komprehensif bahan haram, syubhat, dan berbahaya (E-Numbers) yang diperbarui secara real-time.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-sync"></i></div>
                <h3>BPOM & MUI Sync</h3>
                <p style="margin-top: 16px; color: var(--text-muted); line-height: 1.6;">Sinkronisasi langsung dengan data legal BPOM RI dan LPPOM MUI untuk akurasi data yang tidak terbantahkan.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Siap Memulai Perjalanan Halal Anda?</h2>
        <p style="margin-bottom: 40px; opacity: 0.8; max-width: 600px; margin-left: auto; margin-right: auto;">Bergabunglah dengan jutaan muslim lainnya yang telah mempercayakan keamanan konsumsi mereka pada Halalytics.</p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <a href="#" class="btn btn-primary" style="background: white; color: var(--primary);">
                <i class="fab fa-apple"></i> App Store
            </a>
            <a href="#" class="btn btn-primary" style="background: white; color: var(--primary);">
                <i class="fab fa-google-play"></i> Play Store
            </a>
        </div>
    </section>

    <footer style="padding: 80px; text-align: center; border-top: 1px solid #eee;">
        <div class="logo" style="justify-content: center; margin-bottom: 24px;">
            <div style="background: white; border-radius: 12px; padding: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-right: 12px;">
            <div style="background: white; padding: 6px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); display: inline-block;">
                <img src="{{ asset('images/logo_halalytics.png') }}?v={{ time() }}" alt="Halalytics Logo" style="height: 44px; width: auto; object-fit: contain;">
            </div>
            </div>
            <span style="font-weight: 800; letter-spacing: -0.5px;">Halalytics</span>
        </div>
        <p style="color: var(--text-muted); font-size: 14px;">© 2024 Halalytics Ecosystem. All rights reserved.</p>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.style.padding = '16px 80px';
                nav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
            } else {
                nav.style.padding = '24px 80px';
                nav.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>
