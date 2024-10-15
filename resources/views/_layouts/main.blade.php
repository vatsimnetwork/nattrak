@extends('_layouts.base')
@section('layout')
<body>
<header id="header">
    <div class="">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/natTrak_Logo_2000px.png') }}" alt="natTrak" class="img-fluid" style="max-height: 2.5em;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#authNav" aria-controls="authNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="authNav">
                    <ul class="navbar-nav gap-2">
                        @auth
                            @can('administrate')
                                <li class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">
                                        Admin
                                    </a>
                                    <ul class="dropdown-menu">
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
                                    </ul>
                                </li>
                            @endcan
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ auth()->user()->full_name }} {{ auth()->user()->id }}
                                </a>
                                <ul class="dropdown-menu">
                                    <p class="px-3 mb-0">Mode: {{ auth()->user()->settings()->get('user-mode')->name }}</p>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('auth.mode.select') }}">Switch mode</a></li>
                                    <li><a class="dropdown-item" href="{{ route('auth.deauthenticate') }}">Sign out</a></li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-primary font-display" href="{{ route('auth.redirect') }}" role="button">
                                    Sign In With VATSIM
                                </a>
                            </li>
                            @if (config('app.env') == 'local')
                                <li class="nav-item">
                                    <a href="/auth/1234567" role="button" class="btn btn-outline-primary font-display">
                                        Dev Account
                                    </a>
                                </li>
                            @endif
                        @endauth
                        <li class="nav-item d-flex flex-column text-center text-body">
                            <div>
                                <span>
                                    TMI {{ current_tmi() }}
                                </span>
                            </div>
                            <div>
                                <span>
                                    {{ now()->format('H:i') }} Z
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
@can('activePilot')
    @include('pilots.nav')
@endcan
@canany(['activeBoundaryController', 'activeController'])
    @include('controllers.nav')
@endcanany
<main class="mt-4 mb-4">
    @yield('page')
</main>
<div class="container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <p class="col-md-4 mb-0 text-body-secondary">© {{ date('Y') }} VATSIM, Inc.</p>

        <a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="{{ asset('images/VATSIM_Logo_Official_500px.png') }}" style="height: 50px;" alt="">
        </a>

        <ul class="nav col-md-4 justify-content-end">
            <li class="nav-item"><a href="{{ route('about') }}" class="nav-link px-2 text-body-secondary">About</a></li>
            <li class="nav-item"><a href="https://github.com/vatsimnetwork/nattrak/issues/new" class="nav-link px-2 text-body-secondary">Report Issue <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path d="M3.75 2h3.5a.75.75 0 0 1 0 1.5h-3.5a.25.25 0 0 0-.25.25v8.5c0 .138.112.25.25.25h8.5a.25.25 0 0 0 .25-.25v-3.5a.75.75 0 0 1 1.5 0v3.5A1.75 1.75 0 0 1 12.25 14h-8.5A1.75 1.75 0 0 1 2 12.25v-8.5C2 2.784 2.784 2 3.75 2Zm6.854-1h4.146a.25.25 0 0 1 .25.25v4.146a.25.25 0 0 1-.427.177L13.03 4.03 9.28 7.78a.751.751 0 0 1-1.042-.018.751.751 0 0 1-.018-1.042l3.75-3.75-1.543-1.543A.25.25 0 0 1 10.604 1Z"></path></svg></a></li>
        </ul>
    </footer>
</div>
</body>
@endsection
