@extends('_layouts.main')
@section('page')
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis">Utility</h2>
        <h4 class="font-display mt-5">Clear data</h4>
        <p>This function will clear the database of <span class="font-bold">{{ $countClx }}</span> clearances, {{ $countRcl }} request messages, and {{ $countCpdlc }} CPDLC messages.</p>
        <form action="{{ route('administration.clear-db') }}" method="post">
            @csrf
            <button class="btn btn-danger">Clear</button>
        </form>
    </div>
@endsection
