<div class="container">
    <nav class="navbar navbar-expand-lg p-1 rounded-3 mt-3 bg-vatsim-indigo" data-bs-theme="dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                @can('activeBoundaryController')
                    Domestic
                @else
                    {{ current_dl_authority() ?? 'Offline' }}
                @endcan
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#controller-nav" aria-controls="controller-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="controller-nav">
                <ul class="navbar-nav gap-2">
                    @can('activeController')
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('controllers/clx/pending') ? 'active' : '' }}" href="{{ route('controllers.clx.pending') }}">Pending Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('controllers/clx/processed') ? 'active' : '' }}" href="{{ route('controllers.clx.processed') }}">Processed Messages</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('controllers.clx.create') }}" class="nav-link {{ Request::is('controllers/clx/create' ? 'active' : '') }}">Create Manual Clearance</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('domestic.notify-new-eta-for-pilot') }}" class="nav-link">Notify New ETA for Pilot</a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ route('tracks.index') }}" class="nav-link">Tracks</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
