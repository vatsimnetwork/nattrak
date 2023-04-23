<div class="container">
    <nav class="navbar navbar-expand-lg p-2 rounded-3 my-2 border border-primary shadow-sm bg-primary">
        <div class="container-fluid">
            <div class="navbar-brand font-display" href="#">
                <i class="fa-solid fa-plane"></i>
                <span class="ml-2">Pilots</span>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav gap-2">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/message-history') ? 'active' : '' }}" aria-current="page" href="{{ route('pilots.message-history') }}">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/rcl*') ? 'active' : '' }}" href="{{ route('pilots.rcl.index') }}">Request Clearance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/message-history') ? 'active' : '' }}" href="#">Notify New ETA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/message-history') ? 'active' : '' }}" href="#">Help</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
