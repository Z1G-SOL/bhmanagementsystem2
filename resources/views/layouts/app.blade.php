<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Davao-Dorm Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-brand { tracking-spacing: 1px; }
        body { min-height: 100vh; display: flex; flex-direction: column; }
        main { flex: 1; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}">
                🏢 DAVAO-DORM CONNECT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('catalog.index') ? 'active fw-bold' : '' }}" href="{{ route('catalog.index') }}">
                            <i class="bi bi-shop me-1"></i> Marketplace
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-warning fw-bold text-dark px-3" href="{{ route('register') }}">
                                Join as Member
                            </a>
                        </li>
                    @endguest

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white fw-semibold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }} 
                                <span class="badge bg-light text-primary small text-uppercase ms-1" style="font-size: 10px;">
                                    {{ Auth::user()->role }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm animate slideIn" aria-labelledby="userDropdown">
                                <li>
                                    <h6 class="dropdown-header">Navigation Hub</h6>
                                </li>
                                
                                @if(Auth::user()->isLandlord())
                                    <li>
                                        <a class="dropdown-item fw-bold text-primary" href="{{ route('landlord.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i> Landlord Core
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item fw-bold text-success" href="{{ route('renter.dashboard') }}">
                                            <i class="bi bi-columns-gap me-2"></i> Renter Space
                                        </a>
                                    </li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-semibold">
                                            <i class="bi bi-power me-2"></i> Terminate Session
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            &copy; {{ date('Y') }} Davao-Dorm Connect. Operational Infrastructure Secured.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>