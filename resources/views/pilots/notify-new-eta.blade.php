@extends('_layouts.main')
@section('page')
    <div class="container">
        <h1 class="fs-2 mb-4 font-display text-primary-emphasis">Notify New Entry Time</h1>
        <p>
            You must notify the controller when your estimated entry time for oceanic airspace changes by 5 or more minutes.
        </p>
        <livewire:pilots.notify-new-eta/>
    </div>
@endsection
