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
    
    <link rel="stylesheet" href="{{ asset('css/layout-participant.css') }}">
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
