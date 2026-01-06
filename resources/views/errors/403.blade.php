@extends('_layouts.main')
@section('page')
    <div class="container">
        <h1 class="fs-1">
            Error 503 - action unauthorised.

        </h1>
        <p class="font-monospace">
            {{ $exception->getMessage() }}
        </p>
        <p>
            Try signing in again.
        </p>
    </div>
@endsection
