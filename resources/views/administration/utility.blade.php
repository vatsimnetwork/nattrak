@extends('_layouts.main')
@section('page')
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis">Utility</h2>
        <h4 class="font-display mt-5">Clear data</h4>
        <p>This function will clear the database of <span class="font-bold">{{ $countClx }}</span> clearances, {{ $countRcl }} request messages, and {{ $countCpdlc }} CPDLC messages.</p>
        <form action="{{ route('administration.clear-db') }}" method="post">
            @csrf
            <button class="btn btn-danger">Clear</button>
        </form>
        <h4 class="font-display mt-5">CTP bookings ({{ $countBookings }} found)</h4>
        <form action="{{ route('administration.populate-ctp') }}" method="post">
            @csrf
            <button class="btn btn-primary">Populate CTP bookings</button>
        </form>
        <form action="{{ route('administration.clear-ctp') }}" method="post">
            @csrf
            <button class="btn btn-danger mt-2">Clear CTP bookings</button>
        </form>
        <h4 class="font-display mt-5">.env Settings</h4>
        <p>Automatic acknowledgement of RCL: {{ config('app.rcl_auto_acknowledgement_enabled') ? 'On' : 'Off' }}</p>
        <p>Time constraints on requesting RCL: {{ config('app.rcl_time_constraints_enabled') ? 'On' : 'Off' }}</p>
        <p>Upper time constraint: {{ config('app.rcl_upper_limit') - 1}} minutes</p>
        <p>Lower time constraint: {{ config('app.rcl_lower_limit') + 1 }} minutes</p>
        <p>Automatic track updates: {{ config('services.tracks.auto_update') ? 'On' : 'Off' }}</p>
        <p>TMI override: {{ config('services.tracks.override_tmi') ? 'On ('.config('services.tracks.override_tmi').')' : 'Off' }}</p>
        <p>CTP mode: {{ config('services.vatsim.ctp') ? 'On' : 'Off'}}</p>
    </div>
@endsection
