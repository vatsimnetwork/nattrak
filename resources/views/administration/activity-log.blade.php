@extends('_layouts.main')
@section('page')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis">Activity log</h2>
        <livewire:administration.activity-log-table/>
    </div>
@endsection
