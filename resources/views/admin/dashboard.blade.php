<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0712">
    <meta name="description" content="GhostOrchid administrator dashboard.">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ghostorchid-logo.png') }}?v=2">

    <title>Admin Dashboard | GhostOrchid</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body class="app-body admin-body">

    <div class="app-shell">

        <aside class="sidebar" id="adminSidebar" aria-label="Administrator navigation" aria-hidden="false">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="brand" aria-label="GhostOrchid Admin Dashboard">
                    <span class="brand-mark">
                        <i class="fa-solid fa-spa" aria-hidden="true"></i>
                    </span>

                    <span class="brand-copy">
                        <strong>GHOSTORCHID</strong>
                        <small>ADMIN CONTROL</small>
                    </span>
                </a>

                <button class="sidebar-close" id="sidebarClose" type="button" aria-label="Close navigation">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <div class="sidebar-label">MANAGEMENT</div>

            <nav aria-label="Main navigation">
                <a class="side-link active" href="{{ route('admin.dashboard') }}" aria-current="page">
                    <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                    <span>Overview</span>
                </a>

                <a class="side-link" href="{{ route('admin.users.index') }}">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    <span>Clients</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="security-note">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>

                    <span>
                        <b>Administrator</b>
                        <small>Restricted workspace</small>
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button class="secondary-button" type="submit">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                        <span>Sign out</span>
                    </button>
                </form>
            </div>
        </aside>

        <button class="sidebar-backdrop" id="sidebarBackdrop" type="button" aria-label="Close navigation menu"
            tabindex="-1" aria-hidden="true"></button>

        <main class="main-panel" id="mainContent">

            <header class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-button" id="sidebarToggle" type="button"
                        aria-label="Open navigation menu" aria-controls="adminSidebar" aria-expanded="false">
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>

                    <div class="topbar-copy">
                        <span class="eyebrow">
                            <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                            CONTROL CENTER / OVERVIEW
                        </span>

                        <h1>Admin dashboard</h1>

                    </div>
                </div>

            </header>

            <section class="welcome-strip" aria-labelledby="welcomeHeading">
                <div class="welcome-copy">
                    <span class="welcome-kicker">ADMINISTRATION</span>

                    <h2 id="welcomeHeading">
                        Welcome back,<br>
                        {{ auth('admin')->user()->name }}
                        <span aria-hidden="true">✦</span>
                    </h2>

                    <p>
                        Monitor the GhostOrchid community and account activity
                        from one place.
                    </p>
                </div>

                <div class="welcome-orchid" aria-hidden="true">
                    <i class="fa-solid fa-spa"></i>
                </div>
            </section>

            <section class="stats-grid" aria-label="Community statistics">

                @foreach ([['Total clients', $stats['users'] ?? 0, 'fa-users'], ['Active accounts', $stats['active'] ?? 0, 'fa-user-check'], ['Online recently', $stats['online'] ?? 0, 'fa-signal'], ['Messages sent', $stats['messages'] ?? 0, 'fa-comments'], ['Groups created', $stats['groups'] ?? 0, 'fa-people-group']] as $stat)
                    <article class="stat-card">
                        <span class="stat-icon">
                            <i class="fa-solid {{ $stat[2] }}" aria-hidden="true"></i>
                        </span>

                        <span class="stat-label">{{ $stat[0] }}</span>

                        <strong class="stat-value">
                            {{ number_format((int) $stat[1]) }}
                        </strong>

                        <small class="stat-note">Current total</small>
                    </article>
                @endforeach

            </section>
            
            <section class="panel admin-table-panel" aria-labelledby="recentClientsHeading">
                <div class="panel-heading">
                    <div class="panel-heading-content">
                        <span class="section-kicker">COMMUNITY</span>

                        <h3 id="recentClientsHeading">Recently Registered Clients</h3>

                        <p>Manage and review your latest community members.</p>
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="text-link">
                        <span>View all clients</span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="table-scroll clients-table-scroll">
                    <table class="data-table recent-clients-table">
                        <thead>
                            <tr>
                                <th scope="col">CLIENT INFORMATION</th>
                                <th scope="col">ACCOUNT STATUS</th>
                                <th scope="col">DATE REGISTERED</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($recentUsers as $user)
                                @php
                                    $clientStatus = strtolower(trim($user->status ?? 'active'));
                                    $isActive = $clientStatus === 'active';

                                    $clientName = trim($user->name ?? '') ?: 'Unknown User';
                                    $clientEmail = $user->email ?? 'No email available';

                                    $avatar = $user->avatar_url ?: asset('images/ghostorchid-logo.png');
                                @endphp

                                <tr>
                                    <td data-label="Client information">
                                        <div class="table-user">
                                            <div class="table-user-avatar">
                                                <img src="{{ $avatar }}"
                                                    alt="{{ $clientName }}'s profile picture" loading="lazy"
                                                    onerror="this.onerror=null;this.src='{{ asset('images/ghostorchid-logo.png') }}';">
                                            </div>

                                            <div class="table-user-details">
                                                <b>{{ $clientName }}</b>
                                                <span>{{ $clientEmail }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="Account status">
                                        <span
                                            class="status-badge {{ $isActive ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-indicator" aria-hidden="true"></span>
                                            {{ ucfirst($clientStatus) }}
                                        </span>
                                    </td>

                                    <td class="table-date" data-label="Date registered">
                                        <span class="date-primary">
                                            {{ $user->created_at?->format('M d, Y') ?? '—' }}
                                        </span>

                                        <span class="date-secondary">
                                            {{ $user->created_at?->format('h:i A') ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="table-empty">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="fa-solid fa-users" aria-hidden="true"></i>
                                            </div>

                                            <strong>No registered clients yet</strong>

                                            <small>
                                                New community members will appear here once they register.
                                            </small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
             
            </section>

        </main>
    </div>

    <script>
        (() => {
            'use strict';

            const body = document.body;
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.getElementById('sidebarToggle');
            const closeButton = document.getElementById('sidebarClose');
            const backdrop = document.getElementById('sidebarBackdrop');

            const mobileQuery = window.matchMedia('(max-width: 850px)');

            if (!sidebar || !toggle || !closeButton || !backdrop) {
                return;
            }

            const isSidebarOpen = () =>
                body.classList.contains('sidebar-open');

            const openSidebar = () => {
                if (!mobileQuery.matches) {
                    return;
                }

                body.classList.add('sidebar-open');

                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Close navigation menu');

                sidebar.setAttribute('aria-hidden', 'false');
                backdrop.setAttribute('aria-hidden', 'false');

                closeButton.focus();
            };

            const closeSidebar = (returnFocus = false) => {
                body.classList.remove('sidebar-open');

                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open navigation menu');

                sidebar.setAttribute(
                    'aria-hidden',
                    mobileQuery.matches ? 'true' : 'false'
                );

                backdrop.setAttribute('aria-hidden', 'true');

                if (returnFocus && mobileQuery.matches) {
                    toggle.focus();
                }
            };

            toggle.addEventListener('click', () => {
                if (isSidebarOpen()) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            closeButton.addEventListener('click', () => {
                closeSidebar(true);
            });

            backdrop.addEventListener('click', () => {
                closeSidebar(true);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && isSidebarOpen()) {
                    closeSidebar(true);
                }
            });

            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (mobileQuery.matches) {
                        closeSidebar();
                    }
                });
            });

            const handleViewportChange = () => {
                closeSidebar();

                sidebar.setAttribute(
                    'aria-hidden',
                    mobileQuery.matches ? 'true' : 'false'
                );
            };

            if (typeof mobileQuery.addEventListener === 'function') {
                mobileQuery.addEventListener('change', handleViewportChange);
            } else {
                mobileQuery.addListener(handleViewportChange);
            }

            handleViewportChange();
        })();
    </script>

</body>

</html>
