<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwimPool - @yield('title')</title>
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
        }

        .sidebar-brand {
            padding: 25px 15px 15px;
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
            padding-top: 20px;
            list-style: none;
            padding-left: 0;
        }

        .sidebar-nav li {
            width: 100%;
        }

        .sidebar-nav a {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .sidebar-nav a .material-icons {
            width: 25px;
            margin-right: 10px;
            text-align: center;
            font-size: 1.3rem;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--primary-blue);
            background-color: rgba(0, 51, 153, 0.05);
            border-right: 3px solid var(--primary-blue);
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
                    <span class="material-icons">groups</span> Atlet Saya
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="material-icons">group_add</span> Tim Saya
                </a>
            </li>
            <li>
                <a href="#" class="menu-collapse-toggle">
                    <span class="material-icons">emoji_events</span> Kompetisi <span class="material-icons ms-auto" style="font-size: 1.1rem; width: auto; margin-right: 0;">keyboard_arrow_down</span>
                </a>
                <ul class="sidebar-submenu {{ request()->routeIs('participant.competitions.*') ? 'show' : '' }}">
                    <li><a href="{{ route('participant.competitions.explore') }}" class="{{ request()->routeIs('participant.competitions.explore') ? 'active' : '' }}">Eksplor</a></li>
                    <li><a href="{{ route('participant.competitions.diikuti') }}" class="{{ request()->routeIs('participant.competitions.diikuti') ? 'active' : '' }}">Diikuti</a></li>
                    <li><a href="{{ route('participant.competitions.heats') }}" class="{{ request()->routeIs('participant.competitions.heats') ? 'active' : '' }}">Heat & Jalur</a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="material-icons">payments</span> Pembayaran
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="material-icons">download</span> Unduhan
                </a>
            </li>
            <!-- Divider -->
            <li><hr style="margin: 8px 16px; border-color: #e9ecef;"></li>
            <li>
                <a href="{{ route('operator.dashboard') }}" class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}" style="color: #e65100;">
                    <span class="material-icons" style="color:#e65100;">settings_remote</span> Operator / Wasit
                </a>
            </li>
            <li>
                <a href="{{ route('athletes.index') }}" class="{{ request()->routeIs('athletes.index') ? 'active' : '' }}" style="color: #1565c0;">
                    <span class="material-icons" style="color:#1565c0;">manage_accounts</span> Manajemen Atlet
                </a>
            </li>
        </ul>
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
                    <div class="user-avatar">A</div>
                    Hai, Aji
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><span class="material-icons me-2 text-muted" style="font-size: 1.1rem;">person</span> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('login') }}"><span class="material-icons me-2 text-muted" style="font-size: 1.1rem;">logout</span> Keluar</a></li>
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple sidebar collapse logic
        document.querySelector('.menu-collapse-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
