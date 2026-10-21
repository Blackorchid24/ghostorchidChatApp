<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b0812">
    <meta name="color-scheme" content="dark">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ghostorchid-logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/ghostorchid-logo.png') }}">

    <title>@yield('title', 'GhostOrchid Chat') | GhostOrchid</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="app-body">

    <div class="app-shell">

        <div class="sidebar-scrim" id="sidebarScrim" aria-hidden="true"></div>

        <aside class="sidebar" id="sidebar" aria-label="Main navigation" aria-hidden="false">

            <div class="sidebar-header">

                <a href="{{ route('chat.index') }}" class="brand">
                    <span class="brand-mark">
                        <img src="{{ asset('images/ghostorchid-logo.png') }}" alt="GhostOrchid logo" class="brand-logo">
                    </span>

                    <span class="brand-copy">
                        <strong>GHOSTORCHID</strong>
                        <small>PRIVATE MESSAGING</small>
                    </span>
                </a>

                <button type="button" class="icon-button sidebar-close" id="sidebarClose"
                    aria-label="Close navigation menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>

            <div class="sidebar-content">

                <div class="sidebar-label">WORKSPACE</div>

                <nav class="sidebar-nav" aria-label="Workspace">

                    <a class="side-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                        href="{{ route('chat.index') }}" @if (request()->routeIs('chat.*')) aria-current="page" @endif>

                        <span class="side-link-icon">
                            <i class="fa-regular fa-comments"></i>
                        </span>

                        <span>Messages</span>
                        <span class="side-dot"></span>
                    </a>

                    <a class="side-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                        href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.*')) aria-current="page" @endif>

                        <span class="side-link-icon">
                            <i class="fa-regular fa-user"></i>
                        </span>

                        <span>My profile</span>
                    </a>

                </nav>

            </div>

            <div class="sidebar-bottom">

                <div class="security-note">
                    <span class="security-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>

                    <span class="security-copy">
                        <b>Your space, your privacy</b>
                        <small>Stay connected securely.</small>
                    </span>
                </div>

                <div class="user-mini">

                    <img class="user-mini-avatar"
                        src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" alt="User avatar"
                        loading="lazy">

                    <div class="user-mini-info">
                        <b>{{ auth()->user()->name }}</b>
                        <small>{{ auth()->user()->email }}</small>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf

                        <button type="submit" class="icon-button logout-button" title="Log out" aria-label="Log out">
                            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                        </button>
                    </form>

                </div>

            </div>

        </aside>

        <main class="main-panel" id="mainContent">

            <header class="topbar">

                <div class="topbar-left">

                    <button type="button" class="icon-button mobile-menu" id="menuToggle"
                        aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>

                    <div class="topbar-heading">
                        <div class="eyebrow">
                            <span class="eyebrow-brand">GHOSTORCHID</span>
                            <span class="eyebrow-divider">/</span>
                            <span class="eyebrow-section">MESSAGING</span>
                        </div>

                        <h1>@yield('heading', 'Messages')</h1>
                    </div>

                </div>

                <div class="topbar-right">

                    <a href="{{ route('profile.edit') }}" class="top-avatar-link" aria-label="Open my profile"
                        title="My Profile">

                        <img class="top-avatar"
                            src="{{ auth()->user()->avatar_url ?: asset('images/default-avatar.png') }}"
                            alt="Profile avatar" loading="lazy"
                            onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                    </a>

                </div>

            </header>

            <div class="page-feedback" aria-live="polite">

                @if (session('success'))
                    <div class="flash-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="flash-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

            </div>

            <div class="page-content">
                @yield('content')
            </div>

            @stack('scripts')

        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarScrim = document.getElementById('sidebarScrim');

            if (!sidebar || !menuToggle || !sidebarScrim) {
                return;
            }

            const mobileQuery = window.matchMedia('(max-width: 768px)');

            function openSidebar() {
                if (!mobileQuery.matches) {
                    return;
                }

                sidebar.classList.add('is-open');
                sidebarScrim.classList.add('is-visible');

                document.body.classList.add('sidebar-open');

                menuToggle.setAttribute('aria-expanded', 'true');
                menuToggle.setAttribute('aria-label', 'Close navigation menu');

                sidebar.setAttribute('aria-hidden', 'false');
                sidebarScrim.setAttribute('aria-hidden', 'false');
            }

            function closeSidebar() {
                sidebar.classList.remove('is-open');
                sidebarScrim.classList.remove('is-visible');

                document.body.classList.remove('sidebar-open');

                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Open navigation menu');

                sidebar.setAttribute(
                    'aria-hidden',
                    mobileQuery.matches ? 'true' : 'false'
                );

                sidebarScrim.setAttribute('aria-hidden', 'true');
            }

            menuToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('is-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            sidebarClose?.addEventListener('click', closeSidebar);
            sidebarScrim.addEventListener('click', closeSidebar);

            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (mobileQuery.matches) {
                        closeSidebar();
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            function syncSidebar() {
                closeSidebar();
                sidebar.setAttribute(
                    'aria-hidden',
                    mobileQuery.matches ? 'true' : 'false'
                );
            }

            if (mobileQuery.addEventListener) {
                mobileQuery.addEventListener('change', syncSidebar);
            } else {
                mobileQuery.addListener(syncSidebar);
            }

            syncSidebar();
        });
    </script>

</body>

</html>
