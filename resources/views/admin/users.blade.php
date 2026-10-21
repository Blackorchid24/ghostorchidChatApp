<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0712">
    <meta name="description" content="GhostOrchid administrator client management.">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ghostorchid-logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/ghostorchid-logo.png') }}">

    <title>Clients | GhostOrchid Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body class="app-body admin-body">

    <div class="app-shell">

        <aside class="sidebar">

            <a href="{{ route('admin.dashboard') }}" class="brand" aria-label="GhostOrchid Admin Dashboard">
                <span class="brand-mark">
                    <i class="fa-solid fa-spa" aria-hidden="true"></i>
                </span>

                <span class="brand-copy">
                    <strong>GHOSTORCHID</strong>
                    <small>ADMIN CONTROL</small>
                </span>
            </a>

            <div class="sidebar-label">MANAGEMENT</div>

            <a class="side-link" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                <span>Overview</span>
            </a>

            <a class="side-link active" href="{{ route('admin.users.index') }}" aria-current="page">
                <i class="fa-solid fa-users" aria-hidden="true"></i>
                <span>Clients</span>
            </a>

            <div class="sidebar-bottom">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button class="secondary-button full-button" type="submit">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                        <span>Sign out</span>
                    </button>
                </form>
            </div>

        </aside>

        <main class="main-panel">

            <header class="topbar">

                <div class="topbar-left">
                    <div class="topbar-heading">
                        <span class="eyebrow">CONTROL CENTER / COMMUNITY</span>
                        <h1>Client management</h1>
                    </div>
                </div>

                <div class="topbar-right">
                    <a class="secondary-button" href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        <span>Overview</span>
                    </a>
                </div>

            </header>

            @if (session('success'))
                <div class="flash-success" role="status" aria-live="polite">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <section class="panel admin-table-panel">

                <div class="panel-heading">
                    <div>
                        <span class="section-kicker">USER DIRECTORY</span>
                        <h3>Registered clients</h3>
                        <p class="panel-subtitle">Manage registered users and account access.</p>
                    </div>

                    <span class="count-pill">{{ $users->total() }}</span>
                </div>



                <div class="admin-search-container">
                    <form class="admin-search" method="GET" action="{{ url()->current() }}" role="search">
                        <div class="admin-search-field">
                            <span class="search-icon" aria-hidden="true">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>

                            <input type="search" name="q" value="{{ request('q') }}"
                                placeholder="Search name or email..." autocomplete="off" aria-label="Search clients">

                            @if (request('q'))
                                <a href="{{ url()->current() }}" class="search-clear" aria-label="Clear search"
                                    title="Clear search">
                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>

                        <button class="secondary-button admin-search-button" type="submit">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <span>Search</span>
                        </button>
                    </form>
                </div>


                <div class="table-scroll clients-table-scroll">
                    <table class="data-table recent-clients-table">
                        <thead>
                            <tr>
                                <th scope="col">CLIENT</th>
                                <th scope="col">STATUS</th>
                                <th scope="col">LAST SEEN</th>
                                <th scope="col">JOINED</th>
                                <th scope="col">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td data-label="Client">
                                        <div class="table-user">
                                            <div class="table-user-avatar">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                                    loading="lazy">
                                            </div>

                                            <span>
                                                <b>{{ $user->name }}</b>
                                                <small>{{ $user->email }}</small>
                                            </span>
                                        </div>
                                    </td>

                                    <td data-label="Status">
                                        <span
                                            class="status-badge {{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-indicator"></span>
                                            {{ ucfirst($user->status ?? 'active') }}
                                        </span>
                                    </td>

                                    <td data-label="Last seen">
                                        <span class="table-date">
                                            {{ $user->last_seen_at?->diffForHumans() ?? 'Not available' }}
                                        </span>
                                    </td>

                                    <td data-label="Joined">
                                        <span class="table-date">
                                            {{ $user->created_at?->format('M d, Y') ?? 'Not available' }}
                                        </span>
                                    </td>

                                    <td data-label="Action">
                                        <div class="client-action-group">
                                            <form method="POST" action="{{ route('admin.users.status', $user) }}"
                                                onsubmit="return confirm('Are you sure you want to {{ $user->status === 'active' ? 'deactivate' : 'activate' }} this client account?');">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="small-action {{ $user->status === 'active' ? 'danger-action' : '' }}">
                                                    <i class="fa-solid {{ $user->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }}"
                                                        aria-hidden="true"></i>
                                                    <span>{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('WARNING: Permanently delete {{ addslashes($user->name) }}? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="small-action delete-action">
                                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="table-empty">
                                        <div class="empty-state">
                                            <span class="empty-icon">
                                                <i class="fa-solid fa-users-slash" aria-hidden="true"></i>
                                            </span>
                                            <strong>No clients found</strong>
                                            <small>Try changing your search or check back later.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="pagination">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif

            </section>

        </main>

    </div>

</body>

</html>
