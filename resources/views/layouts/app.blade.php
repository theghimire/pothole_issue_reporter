<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IssuePortal') - Local Issue Reporter</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins & Roboto -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-soft: #4a90e2;
            --secondary-bg: #f5f7fa;
            --text-dark: #334e68;
            --dark-slate: #243b53;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--secondary-bg);
            color: var(--text-dark);
        }

        h1,
        h2,
        h3,
        h4,
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .navbar-custom {
            background-color: white;
            border-bottom: 2px solid #e1e7ec;
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-soft) !important;
            font-size: 1.4rem;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary-soft) !important;
        }

        .main-container {
            min-height: 80vh;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-gov,
        .btn-primary {
            background-color: var(--primary-soft);
            border: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-gov:hover,
        .btn-primary:hover {
            background-color: #357abd;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }

        #map {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            border: 2px solid #e1e7ec;
        }

        footer {
            background: var(--dark-slate);
            color: #bcccdc;
            padding: 40px 0;
            margin-top: 60px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    @yield('extra_css')
</head>

<body>

    <!-- Professional Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top no-print">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-geo-alt-fill me-2"></i>Pothole Issue Reporter
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPortal">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navPortal">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('home') }}">{{ __('Home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('report') }}">{{ __('Report New') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('track') }}">{{ __('Track Issue') }}</a>
                    </li>
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle bg-light px-3 rounded-pill border" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-translate me-1"></i>
                            {{ app()->getLocale() == 'en' ? 'English' : 'नेपाली' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
                            <li><a class="dropdown-item" href="{{ route('lang.switch', 'ne') }}">नेपाली (Nepali)</a>
                            </li>
                        </ul>
                    </li>
                    @if(session('admin_username'))
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-danger btn-sm" href="{{ route('admin.logout') }}">Logout</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-secondary btn-sm"
                                href="{{ route('admin.login') }}">{{ __('Admin Login') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 main-container">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="no-print">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="text-white mb-2">Pothole Issue Reporter</h5>
                    <p class="small mb-0">{{ __('contact_info') }}</p>
                    <p class="small">Contact: ward3@tarkeshwormun.gov.np | {{ __('official_portal') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small mb-0">&copy; {{ date('Y') }} All Rights Reserved. Pothole Issue Reporter Team</p>
                    <p class="small">{{ __('Ensuring better roads, one report at a time.') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @yield('extra_js')
</body>

</html>