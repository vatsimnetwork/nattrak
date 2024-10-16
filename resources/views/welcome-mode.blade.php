@extends('_layouts.base')
@section('layout')
    <body style="height: 100%;" class="bg-secondary-subtle">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="p-5 d-flex flex-column align-content-center justify-center align-items-center bg-body rounded-4 shadow">
                <a class="mb-4" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/natTrak_Logo_2000px.png') }}" alt="natTrak" class="img-fluid" style="max-height: 3.5em;">
                </a>
                <h5>{{ auth()->user()->full_name }} {{ auth()->user()->id }}</h5>
                <h3 class="font-display fw-bold text-vatsim-indigo">Welcome, please select a mode</h3>
                <div class="d-grid gap-2 col-6 mx-auto mt-4">
                    <a href="{{ route('auth.mode.store', ['mode' => \App\Enums\AccessLevelEnum::Pilot]) }}" class="btn btn-primary" type="button">Pilot</a>
                    <a href="{{ route('auth.mode.store', ['mode' => \App\Enums\AccessLevelEnum::Controller]) }}" class="btn btn-primary" type="button">Controller</a>
                    @can('administrate')
                        <a href="{{ route('auth.mode.store', ['mode' => \App\Enums\AccessLevelEnum::Administrator]) }}" class="btn btn-primary" type="button">Adminstrator</a>
                    @endcan
                    <a href="{{ route('auth.deauthenticate') }}" class="btn btn-outline-secondary" type="button">Logout</a>
                </div>
                <p class="mt-5 mb-0 text-body-secondary">© {{ date('Y') }} VATSIM, Inc.</p>
            </div>
        </div>
    </body>
@endsection
