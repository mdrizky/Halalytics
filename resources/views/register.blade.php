<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Halalytics</title>
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
        
        .split-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        
        .image-section {
            flex: 1;
            background: url('https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(46, 139, 87, 0.85) 0%, rgba(0, 100, 0, 0.8) 100%);
        }
        
        .image-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 600px;
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .image-content h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .image-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            text-align: left;
            margin: 2rem 0;
        }
        
        .features-list li {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin-right: 15px;
            font-size: 1.5rem;
            color: var(--gold-color);
        }
        
        .register-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: var(--light-color);
            overflow-y: auto;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-header img {
            height: 60px;
            margin-bottom: 1rem;
        }
        
        .logo-header h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .logo-header p {
            color: #666;
            font-size: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background-color: white;
            color: var(--primary-color);
            text-align: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .card-header h3 {
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        .card-body {
            padding: 2.5rem;
            background-color: white;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .form-control {
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
            margin-bottom: 1.25rem;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.85rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-top: 0.5rem;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            background-color: #26784e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 139, 87, 0.3);
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        .social-login {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
            flex-direction: column;
        }
        
        .social-btn {
            padding: 0.8rem;
            border-radius: 10px;
            color: white;
            text-align: center;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            border: 1px solid transparent;
            background-color: white;
            color: #555;
            border: 1px solid #e0e0e0;
        }
        
        .btn-google {
            color: var(--google-color);
        }
        
        .btn-facebook {
            color: var(--facebook-color);
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #d0d0d0;
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
            margin: 1.5rem 0;
            color: #999;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider-text {
            padding: 0 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .login-link a:hover {
            color: #26784e;
            text-decoration: underline;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: -0.8rem;
            margin-bottom: 1rem;
        }
        
        .form-floating label {
            padding-left: 2.5rem;
        }
        
        .form-floating .bi {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            z-index: 5;
        }
        
        .footer {
            text-align: center;
            margin-top: 2rem;
            color: #999;
            font-size: 0.9rem;
        }
        
        @media (max-width: 992px) {
            .split-container {
                flex-direction: column;
            }
            
            .image-section {
                display: none;
            }
            
            .register-section {
                padding: 1.5rem;
                background: url('https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
                background-size: cover;
                position: relative;
            }
            
            .register-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(46, 139, 87, 0.9);
                z-index: 0;
            }
            
            .register-container, 
            .footer {
                position: relative;
                z-index: 1;
            }
            
            .card {
                background: rgba(255, 255, 255, 0.95);
            }
            
            .card-header {
                background: transparent;
            }
            
            .logo-header h2,
            .logo-header p,
            .login-link {
                color: white !important;
            }
            
            .login-link a {
                color: var(--gold-color) !important;
            }
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
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .btn-primary { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Image Section (Left) -->
        <div class="image-section">
            <div class="image-content">
                <h1><i class="bi bi-shield-check"></i> Halalytics</h1>
                <p>Bergabunglah dengan komunitas kami untuk pengalaman verifikasi halal yang lebih baik dan terpercaya</p>
                
                <ul class="features-list">
                    <li>
                        <span class="feature-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </span>
                        <span>Verifikasi produk halal instan dengan teknologi canggih</span>
                    </li>
                    <li>
                        <span class="feature-icon">
                            <i class="bi bi-heart-fill"></i>
                        </span>
                        <span>Analisis nutrisi lengkap untuk hidup lebih sehat</span>
                    </li>
                    <li>
                        <span class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </span>
                        <span>Komunitas aktif yang saling mendukung</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Register Section (Right) -->
        <div class="register-section">
            <div class="register-container">
               
                <div class="card">
                    <div class="card-header">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('registeraction') }}">
                            @csrf
                            <div class="form-group">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person-badge"></i> Username
                                </label>
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" 
                                       name="username" value="{{ old('username') }}" required placeholder="Enter your username">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password" placeholder="Create password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="form-label">
                                    <i class="bi bi-lock-fill"></i> Confirm Password
                                </label>
                                <input id="password-confirm" type="password" class="form-control" 
                                       name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password">
                            </div>

                            <input type="hidden" name="role" value="PENGGUNA">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Create Account
                            </button>
                        </form>

                        <div class="divider">
                            <span class="divider-text">Or register with</span>
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

                        <div class="login-link">
                            Already have an account? <a href="{{ url('/') }}">Sign in</a>
                        </div>
                    </div>
                </div>
                
                <div class="footer">
                    &copy; 2025 Halalytics. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>