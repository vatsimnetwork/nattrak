@extends('_layouts.main')
@section('page')
    @if (auth()->check() && (!auth()->user()->can('activePilot') && !auth()->user()->can('activeController')))
        <div class="alert alert-danger">
            <b>You must be connected as either a pilot or oceanic controller to access natTrak functionality.</b>
        </div>
    @elseif (!auth()->check())
        <div class="alert alert-danger">
            <b>You must be logged in (via the nav bar) and be connected as either a pilot or oceanic controller to access natTrak functionality.</b>
        </div>
    @endif
    <img src="images/newsandnotams.png" class="img-fluid py-3" style="height: 100px;" />
    <div class="row mt-4">
        @if (count($notams) == 0)
            <div class="col">
                No NOTAMs available.
            </div>
        @endif
        @foreach ($notams as $notam)
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-body">{{ $notam->title }}</h6>
                        @if ($notam->subtitle)
                            <h6 class="card-subtitle mb-2 text-muted">{{ $notam->subtitle }}</h6>
                        @endif
                        <p class="text-body">{{ $notam->content }}</p>
                        @if ($notam->url)
                            <a href="{{ $notam->url }}" class="card-link">Read more</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
