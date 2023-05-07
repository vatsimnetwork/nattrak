@extends('_layouts.main')
@section('page')
    <div class="container">
        <div class="mb-4 d-flex flex-row justify-content-between align-items-center">
            <h2 class="fs-2 font-display text-primary-emphasis">NOTAMs</h2>
            @can('create', \App\Models\Bulletin::class)
                <div>
                    <a href="{{ route('notams.create') }}" class="btn btn-outline-primary">Create</a>
                </div>
            @endcan
        </div>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach($bulletins as $bulletin)
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $bulletin->title }}</h5>
                            <p class="card-subtitle mb-2 text-body-secondary">
                                {{ $bulletin->created_at->toDayDateTimeString() }}
                            </p>
                            @if ($bulletin->subtitle)
                                <p class="fst-italic">
                                    {{ $bulletin->subtitle }}
                                </p>
                            @endif
                            <p>{{ $bulletin->content }}</p>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between align-items-center">
                                @if ($bulletin->action_url)
                                    <div>
                                        <a target="_blank" href="{{ $bulletin->action_url }}" class="">Read more</a>
                                    </div>
                                @endif
                                @can('delete', $bulletin)
                                    <div>
                                        <form action="{{ route('notams.destroy', $bulletin) }}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button onclick="return confirm('Are you sure?')" class="btn btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top: 20px;">
            {{ $bulletins->links() }}
        </div>
    </div>
@endsection
