<!DOCTYPE html>
<html lang="en" class="h-100" data-bs-theme="{{ session('darkMode', false) ? 'dark' : 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @vite(['resources/scss/bootstrap.scss', 'resources/scss/site.scss', 'resources/js/app.js', 'resources/css/datatables.css'])
    @livewireStyles
    <title>@if(isset($_pageTitle)) {{ $_pageTitle }} :: @endif natTrak :: VATSIM</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<body>
@if (!Session::get('hideNavBarOnSession', false))
<header id="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ asset('images/natTrak_Logo_2000px.png') }}" class="display-light" alt="natTrak" height="40">
                <img src="{{ asset('images/natTrak_White.png') }}" class="display-dark" alt="natTrak" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#authNav" aria-controls="authNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="authNav">
                <ul class="navbar-nav gap-2 ms-auto">
                    @auth
                        @can('administrate')
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Admin
                                </a>
                                <ul class="dropdown-menu shadow">
                                    <li>
                                        <a href="{{ route('administration.controllers') }}" class="dropdown-item">Controller permissions</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('administration.accounts') }}" class="dropdown-item">Admin permissions</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('administration.activity-log') }}" class="dropdown-item">Activity log</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('tracks.index') }}" class="dropdown-item">Tracks</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('notams.index') }}" class="dropdown-item">NOTAMs</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('administration.utility') }}" class="dropdown-item">Utility</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('administration.datalink-authorities') }}"
                                           class="dropdown-item">Datalink Authorities</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('administration.ctp-bookings') }}" class="dropdown-item">CTP Bookings</a>
                                    </li>
                                </ul>
                            </li>
                        @endcan
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->full_name }} {{ auth()->user()->id }}
                            </a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('auth.deauthenticate') }}">Sign out</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a href="{{ route('account.manage-api-token') }}" class="dropdown-item">Manage API Token</a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('auth.redirect') }}">
                                Sign in
                            </a>
                        </li>
                        @if (config('app.env') == 'local')
                            <li class="nav-item">
                                <a href="/auth/1234567" class="nav-link">
                                    Dev Account
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <span class="navbar-text">
                    TMI {{ current_tmi() }}
                    &middot;
                    {{ now()->format('H:i') }} Z
                </span>
            </div>
        </div>
    </nav>
    @can('activePilot')
        @include('pilots.nav')
    @endcan
    @canany(['activeBoundaryController', 'activeController'])
        @include('controllers.nav')
    @endcanany
</header>
@endif

<main class="my-4" role="main">
    @yield('page')
</main>
<div class="container mt-5">
    <footer class="py-3 my-4">
        <p class="text-center text-body-secondary">A VATSIM service for oceanic clearances in the Atlantic oceanic regions.<br/>Please report issues via Discord.</p>
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item"><a href="{{route('toggleNavBarOnSession')}}" class="nav-link px-2 text-body-secondary">Toggle Navigation Bar</a></li>
            <li class="nav-item"><a href="{{route('toggleDarkMode')}}" class="nav-link px-2 text-body-secondary">Toggle Dark Mode</a></li>
            <li class="nav-item"><a href="https://github.com/vatsimnetwork/nattrak" target="_blank" class="nav-link px-2 text-body-secondary">GitHub</a></li>
        </ul>
        <p class="text-center text-body-secondary">© {{ date('Y') }} VATSIM, Inc</p>
    </footer>
</div>
@livewireScripts
@if (Session::has('alert'))
    <script>
        window.onload = (event) => {
            Swal.fire(
                    <?php echo(json_encode(Session::get('alert'))) ?>
            )
        };
    </script>
@endif
</body>
</html>
