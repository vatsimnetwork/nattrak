<!doctype html>
<html lang="en" class="h-100" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @vite(['resources/scss/bootstrap.scss', 'resources/scss/site.scss', 'resources/js/app.js', 'resources/css/datatables.css'])
    @livewireStyles
    <title>@if(isset($_pageTitle)) {{ $_pageTitle }} :: @endif natTrak :: VATSIM</title>
</head>
<body>
<header id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
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
                                            <a href="#" class="dropdown-item">Tracks</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('notams.index') }}" class="dropdown-item">NOTAMs</a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ auth()->user()->full_name }} {{ auth()->user()->id }}
                                </a>
                                <ul class="dropdown-menu">
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
                        <li class="nav-item">
                            <div href="" class="btn btn-outline-secondary font-display" style="cursor: pointer;">
                                TMI {{ current_tmi() }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    @can('activePilot')
        @include('pilots.nav')
    @endcan
    @can('activeController')
        @include('controllers.nav')
    @endcan
    <main class="mt-4 mb-4">
        @yield('page')
    </main>
    <footer class="mt-5">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                <p class="col-md-4 mb-0 text-body-secondary">A service of <a href="https://vatsim.net">VATSIM</a></p>

                <p class="col-md-4 mb-0 text-center text-body-secondary">Version x</p>

                <ul class="nav col-md-4 justify-content-end">
                    <li class="nav-item"><a href="{{ route('about') }}" class="nav-link px-2 text-body-secondary">About</a></li>
                    <li class="nav-item"><a href="https://github.com/vatsimnetwork/nattrak" class="nav-link px-2 text-body-secondary">GitHub</a></li>
                    <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Report Issue</a></li>
                </ul>
            </div>
        </div>
    </footer>
</header>
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
