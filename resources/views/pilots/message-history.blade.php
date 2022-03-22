@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Message History
            </p><hr />
            <livewire:pilots.message-history/>
        </div>
    </div>
@endsection
