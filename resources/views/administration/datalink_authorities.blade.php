@extends('_layouts.main')
@section('page')
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis">Datalink authorities</h2>
        <livewire:administration.datalink-authorities-table />
        <div class="mt-4">
            <livewire:administration.create-datalink-authority />
        </div>
    </div>
@endsection
