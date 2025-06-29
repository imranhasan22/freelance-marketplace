<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FreelanceHub') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        .navbar-brand {
            font-weight: 600;
            color: #2563eb !important;
        }
        
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        
        .card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: none;
        }
        
        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .footer {
            background-color: #f8f9fa;
            margin-top: auto;
        }
        
        .rating {
            color: #fbbf24;
        }
        
        .badge-status {
            font-size: 0.75rem;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-briefcase me-2"></i>FreelanceHub
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jobs.index') }}">Find Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('services.index') }}">Browse Services</a>
                    </li>
                    @auth
                        @if(auth()->user()->isClient())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('jobs.create') }}">Post a Job</a>
                            </li>
                        @endif
                        @if(auth()->user()->isFreelancer())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('services.create') }}">Create Service</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar ?? '/placeholder.svg?height=32&width=32' }}" 
                                     alt="Avatar" class="rounded-circle me-1" width="32" height="32">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                @if(auth()->user()->isClient())
                                    <li><a class="dropdown-item" href="{{ route('jobs.my-jobs') }}">My Jobs</a></li>
                                @endif
                                @if(auth()->user()->isFreelancer())
                                    <li><a class="dropdown-item" href="{{ route('services.my-services') }}">My Services</a></li>
                                    <li><a class="dropdown-item" href="{{ route('proposals.my-proposals') }}">My Proposals</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>FreelanceHub</h5>
                    <p class="text-muted">Connecting talented freelancers with amazing clients worldwide.</p>
                </div>
                <div class="col-md-3">
                    <h6>For Clients</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('jobs.create') }}" class="text-muted">Post a Job</a></li>
                        <li><a href="{{ route('services.index') }}" class="text-muted">Browse Services</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>For Freelancers</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('jobs.index') }}" class="text-muted">Find Jobs</a></li>
                        <li><a href="{{ route('services.create') }}" class="text-muted">Sell Services</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} FreelanceHub. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-muted me-3">Privacy Policy</a>
                    <a href="#" class="text-muted me-3">Terms of Service</a>
                    <a href="#" class="text-muted">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
