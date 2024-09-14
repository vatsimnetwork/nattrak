@extends('_layouts.main')
@section('page')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="container">
        <h4 class="font-display text-primary-emphasis">Processed messages</h4>
        <h5 class="uk-margin-remove-top">{{ $displayed ? 'Tracks ' . implode(", ", $displayed) : 'No tracks selected' }}</h5>
        <form method="GET" action="{{ route('controllers.clx.processed') }}" class="border p-2 rounded" id="selectTracksForm">
            <div class="mb-2" for="">Select tracks</div>
            <div class="d-flex flex-row justify-content-between align-items-center">
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="display[]" id="RR" value="RR">
                        <label class="form-check-label" for="rr">RR</label>
                    </div>
                    @foreach ($tracks->sortBy('identifier') as $track)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="display[]" value="{{ $track->identifier }}">
                            <label class="form-check-label">{{ $track->identifier }}</label>
                        </div>
                    @endforeach
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input" name="display[]" value="CONC">
                        <label class="form-check-label">CONC</label>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn btn-primary" type="submit">Select</button>
                    <button id="selectAll" class="btn btn-outline-primary">View All</button>
                </div>
            </div>
        </form>
        @if ($displayed)
            <div class="my-3">
                <livewire:controllers.pg-clx-messages :tracks="$displayed"/>
            </div>
        @endif
    </div>
    <script type="module">
        $('#selectAll').click(function (e) {
            e.preventDefault();
            $(':checkbox').each(function () {
                this.checked = true;
            });
            $("#selectTracksForm").submit();
        })

        setInterval(refreshTable, 10000);

        function refreshTable() {
            Livewire.emit('pg:eventRefresh-default');
        }
    </script>
@endsection
