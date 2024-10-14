<div class="container">
    <nav class="navbar navbar-expand-lg p-1 rounded-3 mt-3 bg-vatsim-indigo navbar-dark">
        <div class="container-fluid">
            <div class="navbar-brand font-display" href="#">
                <span class="ml-2">Pilots</span>
            </div>
            <button class="navbar-toggler navbar-dark btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav gap-2 navbar-light">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/message-history') ? 'active' : '' }}" aria-current="page" href="{{ route('pilots.message-history') }}">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/rcl*') ? 'active' : '' }}" href="{{ route('pilots.rcl.create') }}">Request Clearance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/notify-new-eta') ? 'active' : '' }}" href="{{ route('pilots.notify-new-eta') }}">Notify New Entry Time</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://knowledgebase.ganderoceanic.ca/1.0.3/pilots/nattrak/requesting-oceanic-clearance/">Help</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('domestic.notify-new-eta-for-pilot') }}" class="nav-link">Notify New ETA for Pilot</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
