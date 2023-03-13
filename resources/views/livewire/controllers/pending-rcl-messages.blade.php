<div>
    <div @if (config('services.clx-filtering.update.poll_for_updates') && config('services.clx-filtering.update.auto_populate_table')) wire:poll.2s @endif>
        @if (config('services.clx-filtering.update.poll_for_updates') && config('services.clx-filtering.update.auto_populate_table'))
            <p class="uk-text-meta">Automatic table updates enabled.</p>
        @endif
        <table id="dataTable" class="dataTable uk-table uk-table-hover uk-table-striped uk-table-condensed">
            <thead>
            <tr>
                <th>CS</th>
                <th>DEST</th>
                <th>ROUTE</th>
                <th>TRACK</th>
                <th>ENTRY</th>
                <th>ETA</th>
                <th>FL</th>
                <th>MFL</th>
                <th>MACH</th>
                <th>REQ TIME</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($pendingRclMsgs as $msg)
                <tr>
                    <td class="uk-text-bold">{{ $msg->callsign }} {{ $msg->is_concorde ? '(CONC)' : '' }}</td>
                    <td>{{ $msg->destination }}
                    <td>{{ $msg->track ? 'NAT' : 'RR' }}</td>
                    <td>{{ $msg->track?->identifier }}</td>
                    <td>{{ $msg->entry_fix }}</td>
                    <td>{{ $msg->entry_time }}</td>
                    <td>{{ $msg->flight_level }}</td>
                    <td>{{ $msg->max_flight_level ?? 'N/A' }}</td>
                    <td>{{ $msg->mach }}</td>
                    <td>{{ $msg->request_time->format('Hi') }}</td>
                    <td>
                        <a href="{{ route('controllers.clx.show-rcl-message', $msg) }}" class="uk-button uk-button-small uk-button-primary">ACTION</a>
                        @if ($msg->isEditLocked())
                            <span class="uk-text-danger"><i class="fas fa-lock"></i></span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('controllers.clx.delete-rcl-message', $msg) }}">
                            @csrf
                            <button class="uk-button uk-button-small" onclick="return confirm('Are you sure?')">DEL</button>
                            @if ($msg->isEditLocked())
                                <span class="uk-text-danger"><i class="fas fa-lock"></i></span>
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
{{--        <script type="module">--}}
{{--            $(document).ready(function () {--}}
{{--                let table = new DataTable('#dataTable', {--}}
{{--                    responsive: true--}}
{{--                });--}}
{{--            })--}}
{{--        </script>--}}
    </div>
</div>
