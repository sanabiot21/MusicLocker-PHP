<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Music Locker')</title>
    <style>
        /* Prevent collapse FOUC before Bootstrap CSS loads */
        .collapse:not(.show) { display: none; }
    </style>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kode+Mono:wght@400..700&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @if(file_exists(public_path('hot')))
        {{-- Vite dev server is running --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @elseif(file_exists(public_path('build/manifest.json')))
        {{-- Use built assets from manifest --}}
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        @if(isset($manifest['resources/css/app.css']['file']))
            <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        @endif
        @if(isset($manifest['resources/js/app.js']['file']))
            <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
        @endif
    @else
        {{-- Fallback: try @vite directive anyway --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')
</head>
<body class="bg-pattern">
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark-techno fixed-top" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-music-note-beamed me-2"></i>Music Locker
            </a>
            <!-- Offline Status Indicator -->
            <span id="offline-indicator" class="badge bg-warning text-dark ms-2" style="display: none;">
                <i class="bi bi-wifi-off me-1"></i>Offline
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <!-- Authenticated Navigation -->
                        @if(auth()->user()->role === 'admin')
                        <li class="nav-item dropdown">
                            @php
                                $pendingResets = \App\Models\User::whereNotNull('reset_requested_at')->whereNull('reset_token')->count();
                                $pendingRecoveries = \App\Models\AccountRecoveryRequest::where('status', 'pending')->count();
                            @endphp
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-shield-check me-1"></i>Admin
                                @if($pendingResets + $pendingRecoveries > 0)
                                    <span class="badge rounded-pill bg-danger ms-1">{{ $pendingResets + $pendingRecoveries }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users') }}"><i class="bi bi-people me-2"></i>Users</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.recovery.requests') }}">
                                    <i class="bi bi-shield-exclamation me-2"></i>Recovery
                                    @if($pendingRecoveries > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingRecoveries }}</span>
                                    @endif
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.system.health') }}"><i class="bi bi-cpu me-2"></i>System Health</a></li>
                            </ul>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('music.*') ? 'active' : '' }}" href="{{ route('music.index') }}">
                                <i class="bi bi-music-note-list me-1"></i>My Music
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('playlists.*') ? 'active' : '' }}" href="{{ route('playlists.index') }}">
                                <i class="bi bi-collection-play me-1"></i>Playlists
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ explode(' ', auth()->user()->first_name)[0] }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- Guest Navigation -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-house me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i>Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <main id="main-content" class="@yield('main_class')">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-dark mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center">
                        <h5 class="mb-3">
                            <i class="bi bi-music-note-beamed me-2" style="color: var(--accent-blue);"></i>
                            Music Locker
                        </h5>
                        <p class="text-muted mb-3">
                            Your personalized music and albums repository.
                            Organize, discover, and cherish your musical journey.
                        </p>
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a>
                            @guest
                                <a href="{{ route('register') }}" class="text-muted text-decoration-none">Register</a>
                                <a href="{{ route('login') }}" class="text-muted text-decoration-none">Login</a>
                            @else
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Admin Panel</a>
                                @endif
                                <a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
                                <a href="{{ route('music.index') }}" class="text-muted text-decoration-none">My Music</a>
                                <a href="{{ route('playlists.index') }}" class="text-muted text-decoration-none">Playlists</a>
                            @endguest
                        </div>
                        <hr class="my-4" style="border-color: #333;">
                        <p class="text-muted small mb-2">
                            &copy; {{ date('Y') }} Music Locker. Built with <i class="bi bi-heart-fill text-danger"></i> for music enthusiasts.
                        </p>
                        <p class="text-muted small">
                            Developed by:
                            <a href="https://www.facebook.com/reyyyy.naldooo" target="_blank" class="text-decoration-none" style="color: var(--accent-blue);">Reynaldo D. Grande Jr. II</a>,
                            <a href="https://www.facebook.com/louis.jansen.letigio.2024" target="_blank" class="text-decoration-none" style="color: var(--accent-blue);">Louis Jansen G. Letigio</a>,
                            <a href="https://www.facebook.com/shawn.dayanan" target="_blank" class="text-decoration-none" style="color: var(--accent-blue);">Shawn Patrick R. Dayanan</a>,
                            <a href="https://www.facebook.com/uzyx2" target="_blank" class="text-decoration-none" style="color: var(--accent-blue);">Euzyk Kendyl Dayanan</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Custom JavaScript -->
    <script>

        // Emit server flash messages as overlay toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                MusicLocker.showToast(@json(session('success')), 'success');
            @endif
            
            @if(session('error'))
                MusicLocker.showToast(@json(session('error')), 'danger');
            @endif
            
            @if(session('warning'))
                MusicLocker.showToast(@json(session('warning')), 'warning');
            @endif
            
            @if(session('info'))
                MusicLocker.showToast(@json(session('info')), 'info');
            @endif
            
            @if($errors->any())
                @foreach($errors->all() as $error)
                    MusicLocker.showToast(@json($error), 'danger');
                @endforeach
            @endif
        });

        // Add spinning animation for loading states
        const spinnerStyle = document.createElement('style');
        spinnerStyle.textContent = `
            .spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(spinnerStyle);

        // MusicLocker Utility Object
        window.MusicLocker = {
            // Show toast notification
            showToast: function(message, type = 'info') {
                // Create toast container if it doesn't exist
                let container = document.getElementById('toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container';
                    container.className = 'toast-container position-fixed top-0 end-0 p-3';
                    container.style.zIndex = '1055';
                    document.body.appendChild(container);
                }

                // Create toast element
                const toastId = 'toast-' + Date.now();
                const toastHtml = `
                    <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-dark text-light border-secondary">
                            <i class="bi bi-music-note-beamed me-2 text-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'}"></i>
                            <strong class="me-auto">Music Locker</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body bg-dark text-light">
                            ${message}
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', toastHtml);
                const toast = new bootstrap.Toast(document.getElementById(toastId));
                toast.show();

                // Remove toast element after it's hidden
                document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                    this.remove();
                });
            },

            // Show loading indicator
            showLoading: function(message = 'Loading...') {
                console.log('Loading:', message);
                this.showToast(message, 'info');
            },

            // AJAX utility function
            ajax: function(url, options = {}) {
                const defaultOptions = {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                };

                // Merge options
                const config = Object.assign({}, defaultOptions, options);

                // Handle form data for POST requests
                if (config.data && config.method !== 'GET') {
                    if (config.data instanceof FormData) {
                        delete config.headers['Content-Type'];
                        config.body = config.data;
                    } else {
                        config.body = JSON.stringify(config.data);
                    }
                    delete config.data;
                }

                return fetch(url, config)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        throw error;
                    });
            }
        };

        // Initialize on document ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize offline status indicator
            updateOfflineIndicator();
        });

        // Update offline indicator visibility
        function updateOfflineIndicator() {
            const indicator = document.getElementById('offline-indicator');
            if (!indicator) return;

            if (!navigator.onLine) {
                indicator.style.display = 'inline-block';
            } else {
                indicator.style.display = 'none';
            }
        }

        // Listen for online/offline events
        window.addEventListener('online', updateOfflineIndicator);
        window.addEventListener('offline', updateOfflineIndicator);
    </script>

    @stack('scripts')
</body>
</html>
