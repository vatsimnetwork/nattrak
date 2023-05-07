    <div class="container">
    <nav class="navbar navbar-expand-lg p-2 rounded-3 my-2 bg-primary-subtle navbar-light">
        <div class="container-fluid">
            <div class="navbar-brand font-display" href="#">
                <span class="ml-2">{{ current_dl_authority() ?? 'Offline' }}</span>
            </div>
            <button class="navbar-toggler navbar-light btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav gap-2">
                    <div class="vr text-dark mx-2 d-none d-lg-block"></div>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('controllers/clx/pending') ? 'active' : '' }}" aria-current="page" href="{{ route('controllers.clx.pending') }}">Pending Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('controllers/clx/processed') ? 'active' : '' }}" href="{{ route('controllers.clx.processed') }}">Processed Messages</a>
                    </li>
                    <div class="vr text-dark mx-2 d-none d-lg-block"></div>
                </ul>
            </div>
        </div>
    </nav>
</div>
