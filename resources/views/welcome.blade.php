@extends('_layouts.main')
@section('page')
    <div class="container">
        <div class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-center rounded-3 border shadow-lg bg-primary mb-5">
            <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
                <h2 class="font-display text-light">Request and receive your VATSIM oceanic clearance here</h2>
                <p class="lead text-light mt-5">Available for pilots in the Shanwick EGGX, Gander CZQX, Reykjavik BIRD, and Santa Maria LPPO OCAs.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-5">
                    @auth
                    @else
                        <a href="{{ route('auth.redirect') }}" role="button" class="btn btn-secondary btn-lg px-4 me-md-2 fw-bold">Sign in with VATSIM</a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-4 offset-lg-1 p-0 overflow-hidden shadow-lg">
                <img class="rounded-lg-3" src="bootstrap-docs.png" alt="" width="720">
            </div>
        </div>
        <p class="text-body-emphasis font-display fs-2">NOTAMs</p>
        <div class="accordion" id="notamsAccordion">
            @foreach($notams as $notam)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button font-display fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $loop->index }}">
                            {{ $notam->title }}
                        </button>
                    </h2>
                    <div id="{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p class="lead fw-bold">
                                {{ $notam->subtitle }}
                            </p>
                            <p>{{ $notam->content }}</p>
                            @if ($notam->url)
                                <a href="{{ $notam->url }}">Read more</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
