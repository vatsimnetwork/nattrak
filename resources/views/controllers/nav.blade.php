    <div class="container">
    <nav class="navbar navbar-expand-lg p-1 rounded-3 mt-3 bg-vatsim-indigo navbar-dark">
        <div class="container-fluid">
            <div class="navbar-brand font-display" href="#">
                @can('activeBoundaryController')
                    <span class="ml-2">Domestic</span>
                @else
                    <span class="ml-2">{{ current_dl_authority() ?? 'Offline' }}</span>
                @endcan
            </div>
            <button class="navbar-toggler navbar-light btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav gap-2 navbar-light">
                    <div class="vr text-dark mx-2 d-none d-lg-block"></div>
                    @can('activeController')
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('controllers/clx/pending') ? 'active' : '' }}" aria-current="page" href="{{ route('controllers.clx.pending') }}">Pending Messages</a>
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
                    <div class="vr text-dark mx-2 d-none d-lg-block"></div>
                </ul>
            </div>
        </div>
    </nav>
</div>
