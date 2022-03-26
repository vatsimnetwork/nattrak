@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Pending RCL Messages
            </p>
            <p class="lead">{{ $displayedTrack ? 'Displaying track '. $displayedTrack->identifier : 'All tracks + random routeing requests' }}</p>
            <form method="GET" action="{{ route('controllers.clx.pending') }}" class="form-inline">
                <label for="">Change track</label>
                <select name="sortByTrack" id="" class="custom-select custom-select-sm mx-3">
                    <option value="all">All tracks + random routeing requests</option>
{{--                    <option value="rr">Random routeings</option>--}}
                    @foreach ($tracks as $track)
                        <option value="{{ $track->identifier }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary" type="submit">Sort</button>
            </form>
            <hr>
            <livewire:controllers.pending-rcl-messages :track="$displayedTrack"/>
        </div>
    </div>
@endsection
