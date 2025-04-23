@extends('_layouts.main')
@section('page')
    <div>
        <div class="container">
            <livewire:controllers.view-rcl-message :rcl-message="$message" :datalink-authorities="$dlAuthorities" :active-datalink-authority="$activeDlAuthority" :tracks="$tracks" />
        </div>
    </div>
@endsection
