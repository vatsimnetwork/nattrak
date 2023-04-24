@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-right uk-padding-remove-left">
        <div class="uk-flex uk-flex-row uk-flex-between">
            <h2 class="uk-text-bold uk-text-primary">Create NOTAM</h2>
        </div>
        <hr>
        @if ($errors->any())
            <div class="uk-alert uk-alert-danger" role="alert">
                <p class="uk-text-bold">Some input was incorrect.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('notams.store') }}">
            @csrf
            <fieldset class="uk-fieldset">
                <div class="uk-margin">
                    <input class="uk-input" type="text" name="title" placeholder="Title" required>
                </div>
                <div class="uk-margin">
                    <input type="text" class="uk-input" name="subtitle" placeholder="Subtitle (optional)">
                </div>
                <div class="uk-margin">
                    <textarea class="uk-textarea" rows="5" name="content" placeholder="Content" required></textarea>
                </div>
                <div class="uk-margin">
                    <input type="url" class="uk-input" name="action_url" placeholder="Action button URL (optional)">
                </div>
                <div class="uk-margin">
                    <button class="uk-button uk-button-primary">Create</button>
                </div>
            </fieldset>
        </form>
    </div>
@endsection
