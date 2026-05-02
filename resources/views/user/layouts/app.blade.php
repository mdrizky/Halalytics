<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Halalytics User Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/js/app.js'])
    <style>
        :root {
            --brand-900: #083d35;
            --brand-700: #0f5c50;
            --brand-600: #147768;
            --brand-100: #dff4f0;
            --brand-050: #f3fbf9;
            --ink-900: #16211f;
            --ink-700: #4b5b58;
            --line: #d5e4e0;
            --card-shadow: 0 20px 45px rgba(8, 61, 53, 0.08);
        }

        body {
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(20, 119, 104, 0.12), transparent 35%),
                linear-gradient(180deg, #f8fcfb 0%, #eef7f5 100%);
            color: var(--ink-900);
            min-height: 100vh;
        }

        .navbar-shell {
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid rgba(8, 61, 53, 0.08);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand-700), #1b8b7a);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 24px rgba(15, 92, 80, 0.18);
        }

        .nav-pill {
            border-radius: 999px;
            padding: 0.6rem 0.95rem !important;
            font-weight: 700;
            color: var(--ink-700) !important;
        }

        .nav-pill.active,
        .nav-pill:hover {
            background: var(--brand-100);
            color: var(--brand-900) !important;
        }

        .content-wrap {
            padding-top: 2rem;
            padding-bottom: 3rem;
        }

        .surface-card {
            border: 1px solid rgba(8, 61, 53, 0.08);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--card-shadow);
            border-radius: 24px;
        }

        .page-hero {
            background:
                radial-gradient(circle at right top, rgba(255,255,255,0.28), transparent 38%),
                linear-gradient(135deg, var(--brand-900), var(--brand-600));
            color: #fff;
            border-radius: 28px;
            padding: 2rem;
            box-shadow: 0 24px 50px rgba(8, 61, 53, 0.18);
        }

        .badge-soft {
            background: var(--brand-100);
            color: var(--brand-900);
            border: 1px solid rgba(20, 119, 104, 0.14);
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand-700), var(--brand-600));
            border: none;
            color: #fff;
            font-weight: 800;
            box-shadow: 0 14px 30px rgba(15, 92, 80, 0.16);
        }

        .btn-brand:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-900), var(--brand-700));
        }

        .btn-ghost-brand {
            color: var(--brand-900);
            border-color: rgba(8, 61, 53, 0.12);
            background: rgba(255,255,255,0.85);
            font-weight: 700;
        }

        .btn-ghost-brand:hover {
            background: var(--brand-100);
            color: var(--brand-900);
            border-color: transparent;
        }

        .stat-chip {
            border-radius: 20px;
            padding: 1.1rem 1.25rem;
            border: 1px solid rgba(8, 61, 53, 0.08);
            background: #fff;
            box-shadow: var(--card-shadow);
        }

        .product-card {
            border: 1px solid rgba(8, 61, 53, 0.08);
            border-radius: 24px;
            background: rgba(255,255,255,0.95);
            box-shadow: var(--card-shadow);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 26px 50px rgba(8, 61, 53, 0.12);
        }

        .product-thumb {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            background: var(--brand-050);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: 999px;
            padding: 0.42rem .8rem;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .01em;
        }

        .status-halal, .status-completed, .status-paid { background: #dcfce7; color: #047857; }
        .status-syubhat, .status-pending, .status-waiting_confirmation, .status-confirmed { background: #fef3c7; color: #b45309; }
        .status-haram, .status-cancelled, .status-failed, .status-refunded { background: #fee2e2; color: #b91c1c; }
        .status-processing { background: #dbeafe; color: #1d4ed8; }

        .summary-card {
            position: sticky;
            top: 100px;
        }

        footer {
            color: #5c706b;
        }

        @media (max-width: 991.98px) {
            .summary-card {
                position: static;
            }

            .page-hero {
                padding: 1.5rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top navbar-shell">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-3 me-4" href="{{ route('user.home') }}">
                <span class="brand-mark"><i class="fa-solid fa-shield-heart"></i></span>
                <div>
                    <div class="fw-bold text-dark">Halalytics</div>
                    <div class="small text-secondary">User Commerce Portal</div>
                </div>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="userNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('user.home') ? 'active' : '' }}" href="{{ route('user.home') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('user.compose') ? 'active' : '' }}" href="{{ route('user.compose') }}">Compose</a></li>
                    <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('user.products*') ? 'active' : '' }}" href="{{ route('user.products') }}">Produk</a></li>
                    <li class="nav-item"><a class="nav-link nav-pill {{ request()->routeIs('user.reports*') ? 'active' : '' }}" href="{{ route('user.reports') }}">Laporan</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('user.scanner') }}" class="btn btn-sm btn-ghost-brand rounded-pill px-3">
                        <i class="fa-solid fa-barcode me-2"></i>Scan
                    </a>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-white border">
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                            {{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 1)) }}
                        </div>
                        <div class="small">
                            <div class="fw-bold text-dark">{{ Auth::user()->full_name ?? Auth::user()->username }}</div>
                            <div class="text-secondary">{{ Auth::user()->username }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" type="submit">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="content-wrap">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    <div class="fw-bold mb-2">Ada input yang perlu diperbaiki:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="py-4">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="small">Halalytics commerce demo portal untuk presentasi web user flow.</div>
            <div class="small">User demo: `daffa` / `12345678`</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
