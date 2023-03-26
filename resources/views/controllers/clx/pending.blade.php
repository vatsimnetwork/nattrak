@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-left uk-padding-remove-right">
        <h3 class="uk-text-primary uk-margin-remove-top">Pending RCL messages</h3>
        <h5 class="uk-margin-remove-top">{{ $displayed ? 'Tracks ' . implode(", ", $displayed) : 'None selected' }}</h5>
        <form method="GET" action="{{ route('controllers.clx.pending') }}">
            <label for="">Select tracks</label>
            <div class="uk-grid-small" uk-grid>
                <div>
                    <div class="uk-form-controls">
                        <input class="uk-checkbox" type="checkbox" name="display[]" id="RR" value="RR">
                        <label class="uk-form-label" for="rr">RR</label>
                    </div>
                </div>
                @foreach ($tracks->sortBy('identifier') as $track)
                    <div>
                        <div class="uk-form-controls">
                            <input class="uk-checkbox" type="checkbox" name="display[]" value="{{ $track->identifier }}">
                            <label class="uk-form-label">{{ $track->identifier }}</label>
                        </div>
                    </div>
                @endforeach
                <button class="uk-button uk-button-small uk-button-secondary" style="margin-left: 10px;" type="submit">Sort</button>
                <button id="selectAll" class="uk-button uk-button-small" style="margin-left: 10px;">All</button>
            </div>
        </form>
        <hr>
        @if ($displayed)
            <livewire:controllers.pending-rcl-messages :tracks="$displayed"/>
        @endif
    </div>
    <script type="module">
        $('#selectAll').click(function (e) {
            e.preventDefault();
            $(':checkbox').each(function () {
                this.checked = true;
            });
        })
    </script>
@endsection
