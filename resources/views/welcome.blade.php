@extends('_layouts.main')
@section('page')
    @if (auth()->check() && (!auth()->user()->can('activePilot') && !auth()->user()->can('activeController')))
        <div class="alert alert-danger">
            <b>You must be connected as either a pilot or oceanic controller to access natTrak functionality.</b>
        </div>
    @endif
    <img src="images/newsandnotams.png" class="img-fluid py-3" />
@endsection
