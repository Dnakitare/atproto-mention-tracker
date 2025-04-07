<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bluesky Mention Tracker') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="antialiased">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Bluesky Mention Tracker') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="text-center mb-5">
                            <h1 class="display-4">Bluesky Mention Tracker</h1>
                            <p class="lead">Never miss a mention on Bluesky again</p>
                            <div class="mt-4">
                                @guest
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2">Get Started</a>
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Login</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                                @endguest
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-search display-4 text-primary mb-3"></i>
                                        <h3>Track Mentions</h3>
                                        <p>Monitor mentions of your username, hashtags, or keywords across Bluesky.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bell display-4 text-primary mb-3"></i>
                                        <h3>Get Notified</h3>
                                        <p>Receive real-time notifications when someone mentions you or your keywords.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                                        <h3>Stay Informed</h3>
                                        <p>View all your mentions in one place and never miss an important conversation.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h2>How It Works</h2>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item">Create an account and log in to the application.</li>
                                    <li class="list-group-item">Add keywords, usernames, or hashtags you want to track.</li>
                                    <li class="list-group-item">Configure your notification preferences.</li>
                                    <li class="list-group-item">Receive notifications when new mentions are found.</li>
                                    <li class="list-group-item">View all your mentions in the dashboard.</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h2>Features</h2>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Track usernames
                                        <span class="badge bg-primary rounded-pill">@username</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Track hashtags
                                        <span class="badge bg-primary rounded-pill">#hashtag</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Track keywords
                                        <span class="badge bg-primary rounded-pill">keyword</span>
                                    </li>
                                    <li class="list-group-item">Email notifications</li>
                                    <li class="list-group-item">In-app notifications</li>
                                    <li class="list-group-item">Daily digest emails</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mb-5">
                            <h2>Ready to get started?</h2>
                            <p class="lead">Create an account today and never miss a mention on Bluesky again.</p>
                            @guest
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Sign Up Now</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
