<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SwimPool - @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Material Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #003399; /* Adjust to match the specific blue */
            --sidebar-width: 250px;
            --bg-light: #f4f6f9;
        }
        
        body {
            background-color: var(--bg-light);
            font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 30px 15px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .sidebar-brand img {
            max-height: 50px;
        }

        .sidebar-nav {
            padding-top: 25px;
            list-style: none;
            padding-left: 0;
            flex-grow: 1; /* Fills remaining space to push bottom nav down */
            margin-bottom: 0;
            overflow-y: auto;
        }

        .sidebar-nav li {
            width: 100%;
            margin-bottom: 4px;
        }

        .sidebar-nav a {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-nav a .material-icons {
            width: 25px;
            margin-right: 12px;
            text-align: center;
            font-size: 1.3rem;
            transition: color 0.2s ease;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--primary-blue);
            background: linear-gradient(90deg, rgba(0, 51, 153, 0.31) 0%, rgba(255, 255, 255, 0) 100%);
            border-left-color: var(--primary-blue);
        }

        .sidebar-nav a:hover .material-icons, .sidebar-nav a.active .material-icons {
            color: var(--primary-blue);
        }

        /* Sidebar Bottom */
        .sidebar-bottom {
            padding: 20px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .sidebar-warning {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .sidebar-warning-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .sidebar-warning-text {
            font-size: 0.72rem;
            color: #64748b;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        .sidebar-warning-link {
            font-size: 0.75rem;
            font-weight: 700;
            color: #003399;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .sidebar-warning-link:hover {
            color: #001a4d;
            text-decoration: underline;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px 15px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #f8fafc;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.25s ease;
        }
        .logout-btn:hover {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }
        .logout-btn .material-icons {
            font-size: 1.1rem;
            margin-right: 8px;
            color: #f87171;
        }

        /* Submenu */
        .sidebar-submenu {
            list-style: none;
            padding-left: 0;
            display: none;
            background-color: #f8f9fa;
        }
        
        .sidebar-submenu.show {
            display: block;
        }

        .sidebar-submenu a {
            padding: 10px 20px 10px 55px;
            font-size: 0.9rem;
        }

        /* Main Content wrapper */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            height: 70px;
            background-color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: #6c757d;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        /* Page Content */
        .page-content {
            padding: 20px;
            flex-grow: 1;
        }

        .breadcrumb-wrap {
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #eee;
        }

        /* Utilities */
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table-responsive {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .btn-success-custom {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .btn-success-custom:hover {
            background-color: #218838;
            color: white;
        }
        .btn-outline-success-custom {
            color: #28a745;
            border-color: #28a745;
            background: transparent;
        }
        .btn-outline-success-custom:hover {
            background-color: #28a745;
            color: white;
        }
        
        /* Modal Overlay from original file */
        .custom-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="SwimPool Logo" style="max-height: 90px; width: auto; max-width: 80%; margin-bottom: 10px;">
            <div style="font-weight: 800; color: var(--primary-blue); font-size: 1.5rem; text-align: center; line-height: 1;">SwimPool</div>
        </div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('participant.dashboard') }}" class="{{ request()->routeIs('participant.dashboard') ? 'active' : '' }}">
                    <span class="material-icons">dashboard</span> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('participant.athletes') }}" class="{{ request()->routeIs('participant.athletes') ? 'active' : '' }}">
                    <span class="material-icons">groups</span> Daftar Atlet
                </a>
            </li>
            
            <!-- Divider -->
            <li><hr style="margin: 8px 16px; border-color: #e9ecef;"></li>
            <li>
                <a href="{{ route('participant.competitions.heats') }}" class="{{ request()->routeIs('participant.competitions.heats') ? 'active' : '' }}" style="color: #e65100;">
                    <span class="material-icons">emoji_events</span> Heat &amp; Jalur
                </a>
            </li>
        </ul>
        
        <!-- Bottom Section -->
        <div class="sidebar-bottom">
            <div class="sidebar-warning">
                <div class="sidebar-warning-title">Persiapan Lomba</div>
                <div class="sidebar-warning-text">Pastikan semua atlet telah terdaftar sebelum mengelola jadwal.</div>
                <a href="{{ route('participant.competitions.heats') }}" class="sidebar-warning-link">Kelola Heat <span class="material-icons" style="font-size: 0.9rem;">arrow_forward</span></a>
            </div>
            <button type="button" class="logout-btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <span class="material-icons">logout</span> Log Out
            </button>
        </div>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <button class="menu-toggle">
                <span class="material-icons">menu</span>
            </button>
            <div class="dropdown">
                <a class="user-dropdown dropdown-toggle" href="#" role="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="color: white; text-decoration: none;">
                    <div class="user-avatar">{{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'A' }}</div>
                    Hai, {{ Auth::check() ? explode(' ', Auth::user()->name)[0] : 'Admin' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><span class="material-icons me-2 text-muted" style="font-size: 1.1rem;">person</span> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#logoutModal" style="border: none; background: transparent; width: 100%; text-align: left;">
                            <span class="material-icons me-2 text-muted" style="font-size: 1.1rem;">logout</span> Log Out
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        @hasSection('breadcrumb')
            <div class="breadcrumb-wrap">
                @yield('breadcrumb')
            </div>
        @endif

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>

        <footer class="mt-auto py-3 bg-light text-center border-top">
            <div class="container">
                <span class="text-muted" style="font-size: 0.85rem;">Made with ❤️ by SWIMPOOL © 2026</span>
            </div>
        </footer>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-2 pb-4 px-4">
                    <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span class="material-icons text-danger" style="font-size: 2.5rem;">logout</span>
                    </div>
                    <h4 class="fw-bold mb-2" style="color: #1e293b;">Konfirmasi Keluar</h4>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">Apakah Anda yakin ingin keluar dari sistem? Sesi Anda akan diakhiri.</p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; border: 1px solid #e2e8f0;">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4 py-2" style="border-radius: 8px; font-weight: 600; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">Ya, Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
