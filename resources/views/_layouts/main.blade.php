<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @vite(['resources/scss/site.scss', 'resources/js/app.js', 'resources/css/datatables.css'])
    @livewireStyles
    <title>@if(isset($_pageTitle)) {{ $_pageTitle }} :: @endif natTrak :: VATSIM</title>
</head>
<body>
<div class="uk-background-muted uk-height-viewport">
    <div class="uk-background-default">
        <div class="uk-container">
            <nav class="uk-background-default uk-dark" uk-navbar="dropbar: true">
                <div class="uk-navbar-left">
                    <ul class="uk-navbar-nav">
                        <a href="{{ route('welcome') }}" class="uk-navbar-item uk-logo">
                            <img src="{{ asset('images/natTrak_Logo_2000px.png') }}" style="height: 2em;" alt="">
                        </a>
                        <li class="{{ Request::is('pilots/*') ? 'uk-active' : '' }}">
                            <a href="#" class="uk-text-capitalize">Pilots</a>
                            <div class="uk-navbar-dropdown">
                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                    @can('activePilot')
                                        <li style="padding-bottom: 1em;">
                                        <a href="{{ route('pilots.rcl.index') }}">
                                            <button class="uk-button uk-button-primary uk-button-small">
                                                Request Oceanic Clearance
                                            </button>
                                        </a>
                                    </li>
                                        <li>
                                        <a href="{{ route('pilots.message-history') }}">
                                            <div>
                                                Clearance Status
                                                <div class="uk-navbar-subtitle" style="font-size: 0.8em; padding-top: 5px;">Your oceanic clearance and other message history is accessible here.</div>
                                            </div>
                                        </a>
                                    </li>
                                    @else
                                        <div class="uk-alert uk-alert-danger">
                                            We can't detect an active flight on your VATSIM account. Check your are connected to VATSIM as a pilot and then reload this page.
                                        </div>
                                    @endcan
                                    <li class="uk-nav-divider"></li>
                                    <li class="uk-nav-header uk-text-capitalize">Help</li>
                                    <li>
                                        <a target="_blank" href="https://knowledgebase.ganderoceanic.ca/nattrak/requesting-oceanic-clearance/">Requesting Oceanic Clearance</a>
                                        <a target="_blank" href="https://knowledgebase.ganderoceanic.ca/nattrak/receiving-your-clearance/">Receiving Clearance</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @can('activeController')
                            <li class="{{ Request::is('controllers/*') ? 'uk-active' : '' }}">
                            <a href="#" class="uk-text-capitalize">Oceanic Controllers</a>
                            <div class="uk-navbar-dropdown uk-navbar-dropdown-width-2">
                                <div class="uk-navbar-dropdown-grid uk-child-width-1-2" uk-grid>
                                    <div>
                                        <ul class="uk-nav uk-navbar-dropdown-nav">
                                            <li>
                                                <a href="{{ route('controllers.clx.pending') }}">
                                                    Pending RCL Messages
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('controllers.clx.processed') }}">
                                                    Processed RCL Messages
                                                </a>
                                            </li>
                                            <li class="uk-nav-header"></li>
                                            <li class="uk-nav-header uk-text-capitalize">Help</li>
                                            <li>
                                                <a target="_blank" href="https://knowledgebase.ganderoceanic.ca/nattrak/pending-rcl-messages" class="dropdown-item">RCL Messages List</a>
                                                <a target="_blank" href="https://knowledgebase.ganderoceanic.ca//nattrak/issuing-clx" class="dropdown-item">Issuing Clearance</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endcan
                        @can('administrate')
                            <li class="{{ Request::is('administration/*') ? 'active' : '' }}">
                                <a href="" class="uk-text-capitalize">Administration</a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        @if (Auth::user()->access_level >= \App\Enums\AccessLevelEnum::Root)
                                            <li>
                                                <a href="{{ route('administration.accounts') }}">Manage admin users</a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('administration.controllers') }}">Manage controller permissions</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('administration.activity-log') }}">Activity log</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('notams.index') }}">NOTAMs</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan
                    </ul>
                </div>
                <div class="uk-navbar-right">
                    <ul class="uk-navbar-nav">
                        @auth
                            <li>
                                <a href="">
                                    <div>
                                        <i class="fa-solid fa-user" style="padding-right: 5px"></i>
                                        <span class="uk-text-capitalize">
                                            {{ Auth::user()->full_name ?? Auth::user()->id }}
                                        </span>
                                    </div>
                                </a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li>
                                            <a href="{{ route('auth.deauthenticate') }}">
                                                Sign Out
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('auth.redirect') }}">
                                    <button class="uk-button uk-button-primary uk-button-small uk-text-capitalize">
                                        <div class="uk-flex uk-flex-middle">
                                            <i class="fa-solid fa-link" style="padding-right: 0.5em"></i>
                                            <span>Sign In With VATSIM</span>
                                        </div>
                                    </button>
                                </a>
                            </li>
                            @if (config('app.env') == 'local')
                                <li>
                                    <a href="/auth/1234567">
                                        <button class="uk-button uk-button-primary uk-button-small uk-text-capitalize">
                                            <div class="uk-flex uk-flex-middle">
                                                <i class="fa-solid fa-link" style="padding-right: 0.5em"></i>
                                                <span>Sign In With Dev Account</span>
                                            </div>
                                        </button>
                                    </a>
                                </li>
                            @endif
                        @endauth
                        <li>
                            <a>
                                <span class="uk-button uk-button-secondary">TMI {{ current_tmi() }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="uk-navbar-dropbar"></div>
        </div>
    </div>
    <div class="uk-flex-auto">
        @yield('page')
    </div>
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
