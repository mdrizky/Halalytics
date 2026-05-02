<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Halalytics Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin-system.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            overflow: hidden;
            background: white;
        }
        .login-split {
            display: flex;
            width: 100%;
            height: 100%;
        }
        .login-left {
            flex: 1.2;
            background: linear-gradient(135deg, #2D6A4F 0%, #1B4332 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            color: white;
            text-align: center;
            position: relative;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            background: white;
        }
        .brand-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .brand-logo i {
            font-size: 40px;
            color: #2D6A4F;
        }
        .login-title {
            font-size: 42px;
            margin: 0;
            z-index: 1;
        }
        .login-subtitle {
            font-size: 18px;
            color: rgba(255,255,255,0.7);
            max-width: 400px;
            margin-top: 16px;
            z-index: 1;
        }
        .login-form-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        .login-form-title {
            font-size: 28px;
            color: #2D6A4F;
            margin-bottom: 8px;
            font-weight: 800;
        }
        .login-form-subtitle {
            color: #636E72;
            margin-bottom: 40px;
            font-size: 14px;
        }
        .input-group {
            position: relative;
            margin-bottom: 24px;
        }
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #B2BEC3;
            transition: all 0.3s;
        }
        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 48px;
            border-radius: 8px;
            border: 1px solid #E9ECEF;
            background: #F8F9FA;
            font-size: 14px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .input-group input:focus {
            outline: none;
            border-color: #2D6A4F;
            background: white;
            box-shadow: 0 0 0 4px rgba(45, 106, 79, 0.1);
        }
        .input-group input:focus + i {
            color: #2D6A4F;
        }
        .btn-login {
            width: 100%;
            background: #2D6A4F;
            color: white;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(45, 106, 79, 0.2);
        }
        .btn-login:hover {
            background: #1B4332;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(45, 106, 79, 0.3);
        }
        .social-login {
            display: flex;
            gap: 16px;
            margin-top: 32px;
        }
        .btn-social {
            flex: 1;
            padding: 12px;
            border: 1px solid #E9ECEF;
            border-radius: 8px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #2D3436;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-social:hover {
            background: #F8F9FA;
            border-color: #B2BEC3;
        }
        @media (max-width: 992px) {
            .login-left { display: none; }
            .login-right { padding: 40px; }
        }
    </style>
</head>
<body>
    <div class="login-split">
        <div class="login-left">
            <div class="brand-logo">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <h1 class="login-title">Halalytics Portal</h1>
            <p class="login-subtitle">Aplikasi cerdas untuk verifikasi kehalalan produk, nutrisi, dan manajemen kesehatan dalam satu genggaman.</p>
            
            <div style="margin-top: 60px; display: flex; gap: 40px; z-index: 1;">
                <div>
                    <div style="font-size: 24px; font-weight: 800;">10K+</div>
                    <div style="font-size: 12px; opacity: 0.6;">Verified Products</div>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 800;">5K+</div>
                    <div style="font-size: 12px; opacity: 0.6;">Active Users</div>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 800;">100%</div>
                    <div style="font-size: 12px; opacity: 0.6;">Trusted Data</div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form-container">
                <h2 class="login-form-title">Selamat Datang</h2>
                <p class="login-form-subtitle">Silakan masuk untuk mengakses panel administrasi Halalytics.</p>
                
                @if(session('error'))
                    <div style="background: #FFF5F5; color: #C53030; padding: 12px; border-radius: 6px; margin-bottom: 24px; font-size: 14px; border-left: 4px solid #F56565;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('actionLogin') }}">
                    @csrf
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required value="{{ old('username') }}">
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #636E72; cursor: pointer;">
                            <input type="checkbox" name="remember"> Ingat Saya
                        </label>
                        <a href="#" style="font-size: 13px; color: #2D6A4F; font-weight: 600; text-decoration: none;">Lupa Password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">Masuk Sekarang</button>
                </form>
                
                <div style="text-align: center; margin-top: 32px;">
                    <span style="font-size: 13px; color: #B2BEC3; background: white; padding: 0 12px; position: relative; z-index: 1;">Atau masuk dengan</span>
                    <hr style="margin-top: -10px; border: 0; border-top: 1px solid #E9ECEF;">
                </div>
                
                <div class="social-login">
                    <a href="{{ url('/auth/google') }}" class="btn-social">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" width="18"> Google
                    </a>
                    <a href="{{ url('/auth/facebook') }}" class="btn-social">
                        <i class="fab fa-facebook" style="color: #1877F2; font-size: 18px;"></i> Facebook
                    </a>
                </div>
                
                <p style="text-align: center; margin-top: 40px; font-size: 14px; color: #636E72;">
                    Belum punya akun? <a href="{{ url('/register') }}" style="color: #2D6A4F; font-weight: 700; text-decoration: none;">Daftar Gratis</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>