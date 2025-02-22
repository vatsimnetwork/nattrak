@extends('_layouts.main')
@section('page')
    <div class="container">
        <div class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-center rounded-3 border shadow-lg bg-primary mb-5">
            <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
                <h2 class="font-display text-light">Request and receive your VATSIM oceanic clearance here</h2>
                <p class="lead text-light mt-5">Available for pilots in the Shanwick EGGX, Gander CZQX, Reykjavik BIRD, Bodø Oceanic ENOB, Santa Maria LPPO OCAs.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-5">
                    @auth
                        @cannot('activePilot')
                            <div class="alert alert-danger">
                                <h5 class="font-display mb-0">
                                    Connect to VATSIM as a pilot to request oceanic clearance
                                </h5>
                            </div>
                        @endcan
                        @can('activePilot')
                                <a href="{{ route('pilots.rcl.create') }}" role="button" class="btn btn-secondary btn-lg px-4 me-md-2 fw-bold">Start</a>
                            @endcan
                    @else
                        <a href="{{ route('auth.redirect') }}" role="button" class="btn btn-secondary btn-lg px-4 me-md-2 fw-bold">Sign in with VATSIM</a>
                    @endauth
                </div>
            </div>
        </div>
        <p class="text-body-emphasis font-display fs-2">NOTAMs</p>
        <div class="accordion" id="notamsAccordion">
            @foreach($notams as $notam)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ !$loop->first ? 'collapsed' : '' }} font-display fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $loop->index }}">
                            {{ $notam->title }}
                        </button>
                    </h2>
                    <div id="{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p class="fw-bold">
                                {{ $notam->created_at->toDayDateTimeString() }}
                            </p>
                            @if ($notam->subtitle)
                                <p class="fst-italic">{{ $notam->subtitle }}</p>
                            @endif
                            <p>{{ $notam->content }}</p>
                            @if ($notam->action_url)
                                <a href="{{ $notam->action_url }}">Read more</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="my-3">
            <a class="icon-link icon-link-hover" href="{{ route('notams.index') }}">
                All NOTAMs
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </p>
    </div>
@endsection
