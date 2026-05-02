<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Halalytics Portal</title>
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
            overflow-y: auto;
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
            max-width: 440px;
            width: 100%;
            margin: auto;
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
            margin-bottom: 20px;
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
            margin-top: 10px;
        }
        .btn-login:hover {
            background: #1B4332;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(45, 106, 79, 0.3);
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
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="login-title">Join Halalytics</h1>
            <p class="login-subtitle">Mulai langkah sehatmu hari ini. Bergabung dengan ribuan pengguna lainnya untuk memastikan konsumsi yang aman dan halal.</p>
            
            <div style="margin-top: 60px; text-align: left; max-width: 400px; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>Verifikasi Kehalalan Instan</div>
                </div>
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>Analisis Nutrisi Personal</div>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>Donasi Darah & Kesehatan</div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form-container">
                <h2 class="login-form-title">Daftar Akun</h2>
                <p class="login-form-subtitle">Lengkapi data di bawah ini untuk membuat akun baru.</p>
                
                @if($errors->any())
                    <div style="background: #FFF5F5; color: #C53030; padding: 12px; border-radius: 6px; margin-bottom: 24px; font-size: 13px; border-left: 4px solid #F56565;">
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('registeraction') }}">
                    @csrf
                    <div class="input-group">
                        <i class="fas fa-user-tag"></i>
                        <input type="text" name="username" placeholder="Username" required value="{{ old('username') }}">
                    </div>

                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" required value="{{ old('email') }}">
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-shield-alt"></i>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
                    </div>

                    <input type="hidden" name="role" value="PENGGUNA">
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #636E72; cursor: pointer;">
                            <input type="checkbox" required style="margin-top: 3px;"> 
                            <span>Saya setuju dengan <a href="#" style="color: #2D6A4F; font-weight: 600; text-decoration: none;">Syarat & Ketentuan</a> serta <a href="#" style="color: #2D6A4F; font-weight: 600; text-decoration: none;">Kebijakan Privasi</a>.</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-login">Daftar Sekarang</button>
                </form>
                
                <p style="text-align: center; margin-top: 40px; font-size: 14px; color: #636E72;">
                    Sudah memiliki akun? <a href="{{ url('/') }}" style="color: #2D6A4F; font-weight: 700; text-decoration: none;">Masuk Disini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>