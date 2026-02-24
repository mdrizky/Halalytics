<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Halalytics Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/theme-system.css') }}" rel="stylesheet">
    <style>
        :root {
            --jakarta: 'Plus Jakarta Sans', sans-serif;
            --poppins: 'Poppins', sans-serif;
        }
        
        body {
            font-family: var(--jakarta);
            background-color: var(--bg-dark-base);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* PREMIUM SIDEBAR */
        #sidebar {
            min-width: 280px;
            max-width: 280px;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            position: sticky;
            top: 0;
        }

        #sidebar.active {
            margin-left: -280px;
        }

        .sidebar-header {
            padding: 2.5rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-box {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--emerald-500), var(--emerald-700));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2);
            font-size: 1.25rem;
        }

        #sidebar ul.components {
            padding: 0 1.25rem;
        }

        #sidebar ul li {
            margin-bottom: 8px;
        }

        #sidebar ul li a {
            padding: 14px 18px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }

        #sidebar ul li a i {
            font-size: 1.2rem;
            width: 26px;
            text-align: center;
            opacity: 0.8;
        }

        #sidebar ul li a:hover {
            color: var(--emerald-500);
            background: rgba(16, 185, 129, 0.08);
            transform: translateX(4px);
        }

        #sidebar ul li.active > a {
            color: var(--text-primary);
            background: linear-gradient(135deg, var(--emerald-600), var(--emerald-700));
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.25);
        }
        
        #sidebar ul li.active > a i {
            opacity: 1;
        }

        .sidebar-section-title {
            padding: 1.75rem 1.75rem 0.75rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
            font-weight: 800;
            opacity: 0.6;
        }

        /* MAIN CONTENT area */
        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            background: radial-gradient(circle at 100% 0%, rgba(16, 185, 129, 0.08), transparent 600px), 
                        radial-gradient(circle at 0% 100%, rgba(16, 185, 129, 0.05), transparent 600px);
            padding-bottom: 3rem;
        }

        /* NAVBAR */
        .premium-navbar {
            padding: 0.85rem 2.5rem;
            margin: 1.25rem 2rem;
            border-radius: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 100px;
            padding: 0.5rem 1.25rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .search-box:focus-within {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--emerald-500);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .avatar-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            padding: 2px;
            background: linear-gradient(135deg, var(--emerald-400), var(--emerald-600));
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            background: var(--bg-dark-surface);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ANIMATIONS */
        .fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            #sidebar {
                margin-left: -280px;
                position: fixed;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
            }
            .premium-navbar {
                margin: 0.75rem;
                padding: 0.75rem 1.25rem;
            }
        }
    </style>
    @yield('extra_css')
</head>
<body>

<div id="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="glass-sidebar">
        <div class="sidebar-header">
            <div class="logo-box">
                <i class="fas fa-shield-halal text-white"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-white" style="letter-spacing: -0.5px">Halalytics</h5>
                <small class="text-success small fw-bold">Admin Portal</small>
            </div>
        </div>

        <div class="sidebar-section-title">Core Management</div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="{{ Request::is('admin/products*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam-fill"></i> Inventory
                </a>
            </li>
            <li class="{{ Request::is('admin/categories*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags-fill"></i> Categories
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Verification & AI</div>
        <ul class="list-unstyled components">
             <li class="{{ Request::is('admin/monitor*') ? 'active' : '' }}">
                <a href="{{ route('admin.monitor.index') }}">
                    <i class="bi bi-activity"></i> Live Monitor
                </a>
            </li>
            <li class="{{ Request::is('admin/scan*') ? 'active' : '' }}">
                <a href="{{ route('admin.scan.index') }}">
                    <i class="bi bi-qr-code-scan"></i> Scan Logs
                </a>
            </li>
            <li class="{{ Request::is('admin/verification*') ? 'active' : '' }}">
                <a href="{{ route('admin.verification.index') }}">
                    <i class="bi bi-patch-check-fill"></i> AI Verification
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Support & Users</div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('admin/reports*') ? 'active' : '' }}">
                <a href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-exclamation-triangle-fill"></i> Reports
                </a>
            </li>
             <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people-fill"></i> User Accounts
                </a>
            </li>
             <li class="{{ Request::is('admin/banners*') ? 'active' : '' }}">
                <a href="{{ route('admin.banners.index') }}">
                    <i class="bi bi-image-fill"></i> Banner Ads
                </a>
            </li>
        </ul>

        <div class="px-4 mt-4">
             <div class="glass-card p-3 rounded-4 bg-opacity-10 bg-white border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-20 p-2 rounded-3">
                        <i class="bi bi-lightning-charge-fill text-success"></i>
                    </div>
                    <div>
                        <div class="small fw-bold text-white">System Status</div>
                        <div class="text-success small opacity-75 fw-medium">All Healthy</div>
                    </div>
                </div>
             </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar premium-navbar">
            <div class="container-fluid p-0">
                <button type="button" id="sidebarCollapse" class="btn btn-link text-white p-0 me-4">
                    <i class="fas fa-outdent fs-4"></i>
                </button>

                <div class="d-none d-md-flex search-box">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" class="bg-transparent border-0 text-white small" placeholder="Quick search..." style="outline: none; width: 240px;">
                </div>

                <div class="ms-auto d-flex align-items-center gap-4">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-link text-white-50 p-0 position-relative" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger" style="width: 8px; height: 8px; padding: 0; border: 2px solid var(--bg-dark-surface);">
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-card p-0 mt-4 border-0 shadow-2xl overflow-hidden" style="width: 320px;">
                            <li class="p-3 bg-white bg-opacity-5">
                                <h6 class="mb-0 fw-bold text-white">Notifications</h6>
                            </li>
                            <li class="p-2">
                                <a class="dropdown-item rounded-3 p-3 mb-1 bg-white bg-opacity-5" href="#">
                                    <div class="fw-bold text-white small">New Product Request</div>
                                    <div class="text-muted small">A user requested verification for...</div>
                                </a>
                            </li>
                            <li class="p-2 text-center">
                                <a href="#" class="text-success small fw-bold text-decoration-none">Mark all as read</a>
                            </li>
                        </ul>
                    </div>

                    <!-- User Profile -->
                    <div class="dropdown">
                        <button class="btn btn-link p-0 d-flex align-items-center gap-3 text-decoration-none" data-bs-toggle="dropdown">
                            <div class="text-end d-none d-sm-block">
                                <div class="text-white fw-bold small mb-0">{{ Auth::user()->full_name ?? Auth::user()->username }}</div>
                                <div class="text-muted small fw-medium" style="font-size: 0.7rem; opacity: 0.7;">Administrator</div>
                            </div>
                            <div class="avatar-wrapper">
                                <div class="avatar-inner">
                                    @if(Auth::user()->image)
                                        <img src="{{ asset(Auth::user()->image) }}" class="img-fluid" alt="Avatar">
                                    @else
                                        <span class="text-white fw-bold">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</span>
                                    @endif
                                </div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-card p-2 mt-4 border-0 shadow-2xl">
                            <li><a class="dropdown-item rounded-3 py-2" href="#"><i class="bi bi-person me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item rounded-3 py-2" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider border-white border-opacity-10"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout System</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dynamic Content -->
        <main class="fade-in-up">
            @yield('isi')
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
            $(this).find('i').toggleClass('fa-outdent fa-indent');
        });

        // Highlight active menu item
        let currentUrl = "{{ url()->current() }}";
        $('#sidebar ul li a').each(function() {
            if($(this).attr('href') === currentUrl) {
                $(this).parent().addClass('active');
            }
        });
    });
</script>
@yield('scripts')
</body>
</html>
