@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-left uk-padding-remove-right">
        <div>
            <div class="uk-alert uk-alert-primary">
                <p>Welcome to natTrak! Sign in, then select an option from the navigation bar above to get started.</p>
            </div>
        </div>
        @if (auth()->check() && (!auth()->user()->can('activePilot') && !auth()->user()->can('activeController')))
            <div class="uk-alert uk-alert-danger">
                <b>You must be connected as either a pilot or oceanic controller to access natTrak functionality.</b>
            </div>
        @elseif (!auth()->check())
            <div class="uk-alert uk-alert-danger">
                <b>You must be logged in (via the nav bar) and be connected as either a pilot or oceanic controller to access natTrak functionality.</b>
            </div>
        @endif
        <h3>NOTAMs</h3>
        <ul uk-accordion>
            @foreach($notams as $notam)
                <li class="{{ $loop->first ? 'uk-open' : '' }}">
                    <a href="" class="uk-accordion-title">{{ $notam->title }}</a>
                    <div class="uk-accordion-content">
                        <p class="uk-text-italic">
                            {{ $notam->created_at->toDayDateTimeString() }}
                        </p>
                        @if ($notam->subtitle)
                            <p class="uk-text-italic">
                                {{ $notam->subtitle }}
                            </p>
                        @endif
                        <p>
                            {{ $notam->content }}
                        </p>
                        @if ($notam->action_url)
                            <a href="{{ $notam->action_url }}" class="card-link">Read more</a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('notams.index') }}">View all NOTAMs</a>
    </div>
@endsection
