@extends('_layouts.main')
@section('page')
    <div class="container">
        <h1 class="fs-2 font-display text-primary-emphasis">Request oceanic clearance</h1>
        <p>You must request oceanic clearance <span class="fw-bold">30 minutes prior to oceanic entry.</span></p>
        <p>Requests will be rejected if your ETA has past, is under 10 minutes from now, or is over 45 minutes away.</p>
        <p>Need help? Check out the <i>Help</i> pages in the <i>Pilots</i> dropdown menu above.</p>
        @if (config('app.ctp_info_enabled'))
            <p><b>Please note that if you are flying across the oceanic without a CTP slot, you will be delayed and likely asked to move outside of the vertical limits of oceanic airspace.</b></p>
        @endif
        <a class="btn btn-primary" href="{{ route('pilots.rcl.create') }}">Request Clearance</a>
    </div>
@endsection
