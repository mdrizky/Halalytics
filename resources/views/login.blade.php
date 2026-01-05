<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Halalytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/theme-switcher.js') }}"></script>
    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1a5c3a;
            --google-color: #DB4437;
            --facebook-color: #4267B2;
            --gold-color: #FFD700;
            --gradient: linear-gradient(135deg, #2E8B57 0%, #1a5c3a 100%);
        }
        
        /* Dark Theme */
        [data-theme="dark"] {
            --bg-color: #121212;
            --card-bg: #1E1E1E;
            --border-color: #333333;
            --text-primary: #E0E0E0;
            --text-secondary: #A0A0A0;
            --hover-bg: rgba(255, 255, 255, 0.05);
            --shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Light Theme */
        [data-theme="light"] {
            --bg-color: #F8F9FA;
            --card-bg: #FFFFFF;
            --border-color: #E5E7EB;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --hover-bg: rgba(0, 0, 0, 0.05);
            --shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background-color: var(--bg-color);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Theme Switcher Button */
        .theme-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-primary);
            font-size: 1.3rem;
            z-index: 1000;
            box-shadow: var(--shadow);
        }
        
        .theme-switcher:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(46, 139, 87, 0.4);
        }
        
        .split-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        
        .image-section {
            flex: 1.2;
            background: url('https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
        }
        
        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0.9;
        }
        
        .image-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 600px;
            text-align: center;
            padding: 3rem 2rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out;
        }
        
        .image-content h1 {
            font-size: 3.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .image-content p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            line-height: 1.7;
            font-weight: 300;
        }
        
        .features {
            text-align: left;
            margin: 2rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.2rem;
            font-size: 1.1rem;
        }
        
        .feature-item i {
            margin-right: 15px;
            font-size: 1.5rem;
            color: var(--gold-color);
        }
        
        .app-badge {
            display: inline-block;
            margin: 0.5rem;
            width: 160px;
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .app-badge:hover {
            transform: translateY(-5px);
        }
        
        .login-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: var(--bg-color);
            overflow-y: auto;
            position: relative;
            transition: background-color 0.3s ease;
        }
        
        .login-container {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .logo-header .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 20px rgba(46, 139, 87, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .logo-header .logo-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .logo-header h2 {
            color: var(--primary-color);
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
            letter-spacing: 1px;
        }
        
        .logo-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 400;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.4s, box-shadow 0.4s;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: var(--gradient);
            color: white;
            text-align: center;
            padding: 2rem;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }
        
        .card-header h3 {
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
            position: relative;
            z-index: 1;
            letter-spacing: 0.5px;
        }
        
        .card-body {
            padding: 2.5rem;
            background-color: var(--card-bg);
            transition: background-color 0.3s ease;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }
        
        .form-label i {
            margin-right: 12px;
            color: var(--primary-color);
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            transition: all 0.3s;
            font-size: 1rem;
            height: auto;
            background: var(--card-bg);
            color: var(--text-primary);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.15);
            background: var(--card-bg);
            color: var(--text-primary);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #777;
            cursor: pointer;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .btn-primary {
            background: var(--gradient);
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 15px rgba(46, 139, 87, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(46, 139, 87, 0.4);
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary i {
            margin-right: 10px;
        }
        
        .message {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            animation: slideIn 0.5s ease-out;
            display: flex;
            align-items: center;
        }
        
        .message i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .message.error {
            background: rgba(214, 48, 49, 0.15);
            color: #d63031;
            border-left: 4px solid #d63031;
        }
        
        .message.success {
            background: rgba(0, 184, 148, 0.15);
            color: #00b894;
            border-left: 4px solid #00b894;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .social-login {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
            flex-direction: column;
        }
        
        .social-btn {
            padding: 0.9rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid var(--border-color);
            background-color: var(--card-bg);
            color: var(--text-primary);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-google {
            color: var(--google-color);
        }
        
        .btn-facebook {
            color: var(--facebook-color);
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            border-color: var(--primary-color);
            background-color: var(--hover-bg);
        }
        
        .social-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: var(--text-secondary);
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }
        
        .divider-text {
            padding: 0 1.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 500;
        }
        
        .register-link a {
            color: var(--primary-color);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }
        
        .register-link a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: var(--primary-color);
            transition: width 0.3s;
        }
        
        .register-link a:hover {
            color: var(--primary-dark);
        }
        
        .register-link a:hover::after {
            width: 100%;
        }
        
        .footer {
            text-align: center;
            margin-top: 2.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        /* Animation for form elements */
        .form-group {
            animation: slideUp 0.5s ease-out;
            animation-fill-mode: backwards;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Delay for each form element */
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .btn-primary { animation-delay: 0.3s; }
        
        /* Floating animation for decorative elements */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translate(0, 0px); }
            50% { transform: translate(0, 15px); }
            100% { transform: translate(0, -0px); }
        }
        
        @media (max-width: 1200px) {
            .image-section {
                flex: 1;
            }
        }
        
        @media (max-width: 992px) {
            .split-container {
                flex-direction: column;
            }
            
            .image-section {
                display: none;
            }
            
            .login-section {
                padding: 1.5rem;
                background: url('https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
                background-size: cover;
                position: relative;
            }
            
            .login-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: var(--gradient);
                opacity: 0.9;
                z-index: 0;
            }
            
            .login-container, 
            .footer {
                position: relative;
                z-index: 1;
            }
            
            .card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
            }
            
            .card-header {
                background: rgba(46, 139, 87, 0.9);
            }
            
            .logo-header h2,
            .logo-header p,
            .register-link {
                color: white !important;
            }
            
            .register-link a {
                color: var(--gold-color) !important;
            }
            
            .footer {
                color: white;
            }
        }
        
        @media (max-width: 576px) {
            .card-body {
                padding: 2rem 1.5rem;
            }
            
            .logo-header h2 {
                font-size: 2rem;
            }
            
            .image-content h1 {
                font-size: 2.5rem;
            }
            
            .image-content p {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Theme Switcher Button -->
    <button class="theme-switcher" id="themeSwitcher" title="Toggle Theme">
        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>
    
    <div class="split-container">
        <!-- Image Section (Left) -->
        <div class="image-section">
            <div class="image-content floating">
                <h1><i class="bi bi-shield-check"></i> Halalytics</h1>
                <p>Your trusted companion for halal and healthy food choices. Scan products instantly to verify their halal status and nutritional information.</p>
                
                <div class="features">
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Instant halal verification</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Comprehensive product database</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Nutritional information</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>User reviews and ratings</span>
                    </div>
                </div>
                
                <div class="app-download">
                    <p>Get our mobile app</p>
                    <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="Download on the App Store" class="app-badge">
                    <img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" alt="Get it on Google Play" class="app-badge" style="height: 50px;">
                </div>
            </div>
        </div>
        
        <!-- Login Section (Right) -->
        <div class="login-section">
            <div class="login-container">
                <div class="logo-header">
                    <div class="logo-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h2>Halalytics</h2>
                    <p>Your trusted halal verification platform</p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Login to Your Account</h3>
                    </div>
                    <div class="card-body">
                        {{-- ✅ Pesan error --}}
                        @if ($errors->any())
                            <div class="message error">
                                <i class="bi bi-exclamation-triangle"></i>
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{-- ✅ Pesan sukses --}}
                        @if (session('success'))
                            <div class="message success">
                                <i class="bi bi-check-circle"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('actionLogin') }}">
                            @csrf
                            <div class="form-group">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person-circle"></i> Username
                                </label>
                                <div class="input-group">
                                    <input id="username" type="text" class="form-control" 
                                           name="username" required placeholder="Enter your username">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control" 
                                           name="password" required placeholder="Enter your password">
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="forgot-password">
                                    <a href="#">Forgot password?</a>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        </form>

                        <div class="divider">
                            <span class="divider-text">Or continue with</span>
                        </div>

                        <div class="social-login">
                            <a href="{{ url('/auth/google') }}" class="social-btn btn-google">
                                <span class="social-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                        <path fill="currentColor" d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                                    </svg>
                                </span>
                                Continue with Google
                            </a>
                            <a href="{{ url('/auth/facebook') }}" class="social-btn btn-facebook">
                                <span class="social-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                        <path fill="currentColor" d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/>
                                    </svg>
                                </span>
                                Continue with Facebook
                            </a>
                        </div>

                        <div class="register-link">
                            Don't have an account? <a href="{{ url('/register') }}">Register now</a>
                        </div>
                    </div>
                </div>
                
                <div class="footer">
                    &copy; 2025 Halalytics. All rights reserved. | <a href="#" style="color: #999; text-decoration: none;">Privacy Policy</a> | <a href="#" style="color: #999; text-decoration: none;">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Switcher
        (function() {
            const themeSwitcher = document.getElementById('themeSwitcher');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;
            
            const currentTheme = localStorage.getItem('theme') || 'dark';
            
            function setTheme(theme) {
                html.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-moon-stars-fill';
                } else {
                    themeIcon.className = 'bi bi-sun-fill';
                }
            }
            
            setTheme(currentTheme);
            
            themeSwitcher.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });
        })();
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });
        
        // Add focus effect to form inputs
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
        
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>