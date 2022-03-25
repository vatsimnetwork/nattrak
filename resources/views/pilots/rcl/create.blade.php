@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <a href="{{ route('pilots.rcl.index') }}"><i class="fas fa-angle-left"></i> Back</a>
            <p class="header">
                Request Oceanic Clearance
            </p>
            <hr />
            <p>Need help? Check out [this tutorial]</p>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <b>Some input was incorrect.</b>
                    <ul>
                        @foreach ($errors->all() as $error)
                             <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('pilots.rcl.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="callsign">Callsign</label>
                        <input required type="text" class="form-control" name="callsign" id="callsign" placeholder="Enter callsign" value="{{ $callsign }}">
                        @if ($callsign)
                            <small class="form-text text-muted">Your callsign was automatically collected. You may change the callsign if it is incorrect.</small>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="destination">Destination ICAO</label>
                        <input required type="text" class="form-control" name="destination" id="destination" placeholder="Enter destination ICAO (e.g. EGLL)" maxlength="4" value="{{ $arrival_icao }}">
                        @if ($arrival_icao)
                            <small class="form-text text-muted">Your destination was automatically collected. You may change the destination if it is incorrect.</small>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="flight_level">Requested flight level</label>
                        <input required type="text" class="form-control" name="flight_level" id="flight_level" placeholder="e.g. 410" maxlength="3" value="{{ $flight_level }}">
                        @if ($flight_level)
                            <small class="form-text text-muted">Your requested flight level (the altitude on your flight plan) was automatically collected. You may change the flight level if it is incorrect. <b>Ensure you request the flight level allocated to you in your booking details!</b></small>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="max_flight_level">Maximum flight level</label>
                        <input type="text" class="form-control" name="max_flight_level" id="max_flight_level" placeholder="e.g. 410" maxlength="3">
                        <small class="form-text text-muted">This is the highest flight level you can accept.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="mach">Requested mach number</label>
                        <input required type="text" class="form-control" name="mach" id="mach" placeholder="e.g. 080" maxlength="3">
                        <small class="form-text text-muted">Your requested mach number (don't include the dot at the start)</small>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="track_id">Requested NAT Track</label>
                    <select class="form-control" name="track_id">
                        <option value="" selected>None</option>
                        @foreach($tracks as $track)
                            <option value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                        @endforeach
                    </select>
                    <label><i>or</i></label><br>
                    <label for="random_routeing">Requested random routeing</label>
                    <input type="text" class="form-control" name="random_routeing" id="random_routeing" placeholder="e.g. GOMUP 59/20 59/30 58/40 56/50 JANJO">
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="entry_fix">Entry fix</label>
                        <input required type="text" class="form-control" name="entry_fix" id="entry_fix" placeholder="e.g. MALOT" maxlength="7">
                        <small class="form-text text-muted">The first fix in oceanic airspace.</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="entry_time">Estimate time at entry fix</label>
                        <input required type="number" class="form-control" name="entry_time" id="entry_time" placeholder="e.g. 1350">
                        <small class="form-text text-muted">You can find this in your FMC, providing your simulator is set to real time.</small>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="tmi">Current TMI</label>
                    <input required type="text" class="form-control" name="tmi" id="tmi" placeholder="e.g. 135" maxlength="4">
                </div>
                <div class="form-group">
                    <label for="free_text">Free text (optional)</label>
                    <input type="text" class="form-control" name="free_text" id="free_text">
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">Submit Oceanic Clearance Request</button>
            </form>
        </div>
    </div>
@endsection
