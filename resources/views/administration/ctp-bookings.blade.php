@extends('_layouts.main')
@section('page')
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis">CTP Bookings</h2>
        <livewire:administration.ctp-bookings-table />
    </div>
@endsection
