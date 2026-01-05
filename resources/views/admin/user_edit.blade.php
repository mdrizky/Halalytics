<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Halalytics</title>
    
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1A5632;
            --accent-color: #4CAF50;
            --bg-dark: #121212;
            --bg-card: #1E1E1E;
            --bg-hover: #2A2A2A;
            --text-light: #E0E0E0;
            --text-muted: #A0A0A0;
            --border-color: #333333;
            --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            --glow-effect: 0 0 10px rgba(46, 139, 87, 0.3);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
            padding: 20px;
        }
        
        .page-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Header Styles */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--glow-effect);
            font-weight: 500;
        }
        
        /* Card Styles */
        .form-card {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow), var(--glow-effect);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            background-color: #2A2A2A;
            color: var(--text-light);
            font-size: 15px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.2);
            background-color: #2A2A2A;
            color: var(--text-light);
        }
        
        .form-control:disabled {
            background-color: #252525;
            color: var(--text-muted);
            cursor: not-allowed;
        }
        
        .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            background-color: #2A2A2A;
            color: var(--text-light);
            font-size: 15px;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.2);
        }
        
        /* Info Section */
        .info-section {
            margin-top: 30px;
            padding: 20px;
            border-radius: 12px;
            background: #252525;
            border: 1px dashed var(--border-color);
        }
        
        .info-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-light);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 8px;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-active .status-dot {
            background: #38b000;
            box-shadow: 0 0 8px #38b000;
        }
        
        .status-inactive .status-dot {
            background: #e63946;
            box-shadow: 0 0 8px #e63946;
        }
        
        /* Button Styles */
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-back:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), #144026);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 139, 87, 0.4);
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .badge-active {
            background: linear-gradient(135deg, #38b000, #2a8500);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-card {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .button-group {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-user-edit"></i>Edit User
            </h1>
            <span class="user-badge">
                <i class="fas fa-id-card"></i>ID: #{{ $user->id_user }}
            </span>
        </div>

        <div class="form-card">
            <form action="{{ route('admin.user_update', $user->id_user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Username --}}
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>

                {{-- Role --}}
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-user-tag"></i> Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>

                {{-- Status Akun --}}
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-power-off"></i> Status Akun</label>
                    <select name="active" class="form-select" required>
                        <option value="1" {{ $user->active == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $user->active == 0 ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <div class="mt-2">
                        @if($user->active == 1)
                            <span class="badge badge-active status-indicator">
                                <span class="status-dot"></span>Akun saat ini aktif
                            </span>
                        @else
                            <span class="badge badge-inactive status-indicator">
                                <span class="status-dot"></span>Akun saat ini non-aktif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Informasi Tambahan --}}
                <div class="info-section">
                    <h5 class="info-title"><i class="fas fa-info-circle"></i>Informasi Tambahan</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-calendar-plus"></i> Tanggal Daftar</label>
                            <input type="text" class="form-control"
                                   value="{{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-sign-in-alt"></i> Last Login</label>
                            <input type="text" class="form-control"
                                   value="{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d M Y H:i') : 'Belum pernah login' }}" disabled>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label"><i class="fas fa-search"></i> Jumlah Scan</label>
                        <input type="text" class="form-control" value="{{ $user->scans_count ?? 0 }} kali" disabled>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="button-group">
                    <a href="{{ route('admin.user') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan efek animasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const formCard = document.querySelector('.form-card');
            formCard.style.opacity = '0';
            formCard.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                formCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                formCard.style.opacity = '1';
                formCard.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>