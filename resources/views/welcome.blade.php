@extends('_layouts.main')
@section('page')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card text-bg-primary rounded-0 p-2 pt-3">
                    <div class="container">
                        <p class="fs-3 font-display">Obtain oceanic clearance</p>
                        <p class="font-display">Available for pilots in the Shanwick EGGX, Gander CZQX, Reykjavik BIRD, Bodø Oceanic ENOB, Santa Maria LPPO OCAs.</p>
                        @auth
                            @cannot('activePilot')
                                <div class="alert alert-danger">
                                    natTrak requires you to be connected to VATSIM as a pilot to request oceanic clearance. Please advise your oceanic controller if you still cannot proceed.
                                </div>
                            @endcannot
                            @can('activePilot')
                                <a href="{{ route('pilots.rcl.index') }}" class="mt-3 mb-3 btn btn-light rounded-0 font-display" style="width:12em;">Request clearance</a>
                            @endcan
                        @else
                            <a href="{{ route('auth.authenticate') }}" class="mt-3 mb-3 btn btn-light rounded-0 font-display" style="width:12em;">Login with VATSIM</a>
                        @endauth
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 rounded-0 p-2">
                    <div class="container">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a class="icon-link icon-link-hover fs-4" style="text-decoration: none" target="_blank" href="https://knowledgebase.ganderoceanic.ca/pilots/nattrak/requesting-oceanic-clearance/">
                                    <span class="w-100">How to use natTrak</span>
                                    <svg  xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a class="icon-link icon-link-hover fs-4" style="text-decoration: none" href="https://knowledgebase.ganderoceanic.ca/pilots/basics/first-principles/" target="_blank">
                                    <span class="w-100">Oceanic knowledge base</span>
                                    <svg  xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a class="icon-link icon-link-hover fs-4" style="text-decoration: none" href="https://map.vatsim.net" target="_blank">
                                    <span class="w-100">Check oceanic coverage</span>
                                    <svg  xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a class="icon-link icon-link-hover fs-4" style="text-decoration: none" href="{{ route('tracks.index') }}">
                                    <span class="w-100">View NAT tracks</span>
                                    <svg  xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-body-emphasis font-display fs-2 mt-3">NOTAMs</p>
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
