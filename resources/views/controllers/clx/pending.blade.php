@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Pending RCL Messages
            </p>
            <p class="lead">{{ $displayed ? 'Tracks ' . implode(", ", $displayed) : 'None selected' }}</p>
            <form method="GET" action="{{ route('controllers.clx.pending') }}">
                <label for="">Select tracks</label>
                <div class="mb-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="display[]" id="RR" value="RR">
                        <label class="form-check-label" for="rr">RR</label>
                    </div>
                    @foreach ($tracks->sortBy('identifier') as $track)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="display[]" value="{{ $track->identifier }}">
                        <label class="form-check-label">{{ $track->identifier }}</label>
                    </div>
                    @endforeach
                </div>
                <button class="btn btn-sm btn-primary" type="submit">Sort</button>
            </form>
            <hr>
            @if ($displayed)
                <livewire:controllers.pending-rcl-messages :tracks="$displayed"/>
            @endif
        </div>
    </div>
@endsection
