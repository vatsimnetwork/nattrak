@extends('_layouts.base')
@section('layout')
    <body style="height: 100%;" class="bg-secondary-subtle">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="p-5 d-flex flex-column align-content-center justify-center align-items-center bg-body rounded-4 shadow">
                <a class="mb-4" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/natTrak_Logo_2000px.png') }}" alt="natTrak" class="img-fluid" style="max-height: 3.5em;">
                </a>
                <h3 class="font-display fw-bold text-vatsim-indigo">Welcome, please login with VATSIM</h3>
                <div class="d-grid gap-2 col-6 mx-auto mt-4">
                    <a href="{{ route('auth.redirect') }}" class="btn btn-primary">Login with VATSIM</a>
                    @if (config('app.env') == 'local')
                        <a href="/auth/1234567" class="btn btn-outline-primary">
                            Dev Account
                        </a>
                    @endif
                    <div class="d-flex my-1 flex-row justify-center align-self-center align-items-center text-center">
                        <a href="{{ route('tracks.index') }}" class="btn btn-link text-secondary flex-grow-1">Tracks</a>
                        <a href="{{ route('notams.index') }}" class="btn btn-link text-secondary flex-grow-1">NOTAMs</a>
                    </div>
                </div>
                <p class="mt-5 mb-0 text-body-secondary">© {{ date('Y') }} VATSIM, Inc.</p>
            </div>
        </div>
    </body>
@endsection
