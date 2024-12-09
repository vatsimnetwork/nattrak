<!DOCTYPE html>
<html lang="en" class="h-100" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @vite(['resources/scss/bootstrap.scss', 'resources/scss/site.scss', 'resources/js/app.js', 'resources/css/datatables.css'])
    @livewireStyles
    <title>@if(isset($_pageTitle)) {{ $_pageTitle }} :: @endif natTrak :: VATSIM</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<body>
<header id="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ asset('images/natTrak_Logo_2000px.png') }}" alt="natTrak" height="40">
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
                                </ul>
                            </li>
                        @endcan
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->full_name }} {{ auth()->user()->id }}
                            </a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('auth.deauthenticate') }}">Sign out</a></li>
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
</header>
@can('activePilot')
    @include('pilots.nav')
@endcan
@canany(['activeBoundaryController', 'activeController'])
    @include('controllers.nav')
@endcanany
<main class="my-4" role="main">
    @yield('page')
</main>
<footer class="container py-3 my-4 border-top text-center text-muted small">
    <a href="{{ route('about') }}">About natTrak</a>
    <br>
    Copyright © {{ date('Y') }} VATSIM, Inc.
</footer>
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
