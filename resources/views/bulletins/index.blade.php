@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-right uk-padding-remove-left">
        <div class="uk-flex uk-flex-row uk-flex-between">
            <h2 class="uk-text-bold uk-text-primary">NOTAMs</h2>
            @can('create', \App\Models\Bulletin::class)
                <a href="{{ route('notams.create') }}" class="uk-button uk-button-default">Create</a>
            @endcan
        </div>
        <hr>
        <div class="uk-grid-medium uk-child-width-1-2@s uk-grid-match" uk-grid>
        @foreach($bulletins as $bulletin)
            <div>
                <div class="uk-card uk-card-default">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom">{{ $bulletin->title }}</h3>
                                <p class="uk-text-meta uk-margin-remove-top">
                                    {{ $bulletin->created_at->toDayDateTimeString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        @if ($bulletin->subtitle)
                            <p class="uk-text-italic">
                                {{ $bulletin->subtitle }}
                            </p>
                        @endif
                        <p>{{ $bulletin->content }}</p>
                    </div>
                    <div class="uk-card-footer">
                        <div class="uk-flex uk-flex-row uk-flex-between">
                        @if ($bulletin->action_url)
                            <div>
                                <a target="_blank" href="{{ $bulletin->action_url }}" class="uk-button uk-button-text">Read more</a>
                            </div>
                        @endif
                        @can('delete', $bulletin)
                            <div>
                                <form action="{{ route('notams.destroy', $bulletin) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button class="uk-button uk-button-danger">Delete</button>
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
