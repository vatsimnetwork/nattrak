<div class="">
    <nav class="navbar navbar-expand-lg p-1 bg-secondary-subtle">
        <div class="container">
            <span class="navbar-brand">
                Pilots
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#pilot-nav" aria-controls="pilot-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="pilot-nav">
                <ul class="nav nav-pills gap-2">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/message-history') ? 'active' : '' }}" href="{{ route('pilots.message-history') }}">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/rcl*') ? 'active' : '' }}" href="{{ route('pilots.rcl.create') }}">Request Clearance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pilots/notify-new-eta') ? 'active' : '' }}" href="{{ route('pilots.notify-new-eta') }}">Notify New Entry Time</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://knowledgebase.ganderoceanic.ca/pilots/nattrak/requesting-oceanic-clearance/">Help</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
