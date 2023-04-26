@extends('_layouts.main')
@section('page')
    <div class="container">
        <h1 class="fs-2 font-display text-primary-emphasis">Request oceanic clearance</h1>
        <p>You must request oceanic clearance at least <b>30 minutes before oceanic entry</b> and <b>not 2 hours beforehand (not on the ground!).</b></p>
        <p>Need help? Check out the <i>Help</i> pages in the <i>Pilots</i> dropdown menu above.</p>
        @if (config('app.ctp_info_enabled'))
            <p><b>Please note that if you are flying across the oceanic without a CTP slot, you will be delayed and likely asked to move outside of the vertical limits of oceanic airspace.</b></p>
        @endif
        <a class="btn btn-primary" href="{{ route('pilots.rcl.create') }}">Request Clearance</a>
    </div>
@endsection
