@extends('_layouts.base')
@section('layout')
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
                                </ul>
                            </li>
                        @endcan
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->full_name }} {{ auth()->user()->id }}
                            </a>
                            <ul class="dropdown-menu shadow">
                                <p class="px-3 mb-0">Mode: {{ auth()->user()->settings()->get('user-mode')->name }}</p>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('auth.mode.select') }}">Switch mode</a></li>
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
</body>
@endsection
