@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Request Oceanic Clearance
            </p><hr />
            <p>You must request oceanic clearance at least <b>30 minutes before oceanic entry.</b></p>
            <p>Need help? Check out [this tutorial]</p>
            <p><b>Please note that if you are flying across the oceanic without a CTP slot, you will be delayed and likely asked to move outside of the vertical limits of oceanic airspace.</b></p>
            <a class="btn btn-primary" href="{{ route('pilots.rcl.create') }}">Request Clearance</a>
        </div>
    </div>
@endsection
