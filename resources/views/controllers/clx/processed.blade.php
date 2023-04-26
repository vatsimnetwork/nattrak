@extends('_layouts.main')
@section('page')
    <div class="container">
        <h3 class="uk-text-primary uk-margin-remove-top">Processed RCL messages</h3>
        <h5 class="uk-margin-remove-top">{{ $displayed ? 'Tracks ' . implode(", ", $displayed) : 'None selected' }}</h5>
        <form method="GET" action="{{ route('controllers.clx.processed') }}">
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
                <div>
                    <div class="uk-form-controls">
                        <input type="checkbox" class="uk-checkbox" name="display[]" value="CONC">
                        <label class="uk-form-label">CONC</label>
                    </div>
                </div>
                <button class="uk-button uk-button-small uk-button-secondary" style="margin-left: 10px;" type="submit">Sort</button>
                <button id="selectAll" class="uk-button uk-button-small" style="margin-left: 10px;">All</button>
            </div>
        </form>
        <hr>
        @if ($messages->count() == 0)
            No processed RCL messages.
        @endif
        <div>
            <table id="dataTable" class="dataTable uk-table uk-table-hover uk-table-striped uk-table-condensed">
                <thead>
                <tr>
                    <th>CS</th>
                    <th>DEST</th>
                    <th>ROUTE</th>
                    <th>ENTRY</th>
                    <th>AT</th>
                    <th>FL</th>
                    <th>MACH</th>
                    <th>CLRD TIME</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($messages as $msg)
                        <tr>
                            <td>{{ $msg->rclMessage->callsign }} {{ $msg->rclMessage->is_concorde ? '(CONC)' : '' }}</td>
                            <td>{{ $msg->rclMessage->destination }}
                            <td>
                                {{ $msg->track ? 'NAT '. $msg->track->identifier : 'RR' }} {{ $msg->routeing_changed ? '*' : ''}}
                            </td>
                            <td>{{ $msg->entry_fix }}</td>
                            <td data-order="{{ $msg->entry_time_restriction }}">
                                {{ $msg->entry_time_restriction }} {{ $msg->raw_entry_time_restriction != $msg->rclMessage->entry_time ? '*' : ''  }}
                            </td>
                            <td data-order="{{ $msg->flight_level }}">
                                {{ $msg->flight_level }}{{ $msg->flight_level != $msg->rclMessage->flight_level ? '*' : ''}}
                            </td>
                            <td data-order="{{ $msg->mach }}">
                                {{ $msg->mach }}{{ $msg->mach != $msg->rclMessage->mach ? '*' : ''}}
                            </td>
                            <td>{{ $msg->created_at->format('Hi') }}</td>
                            <td>
                                <a href="{{ route('controllers.clx.show-rcl-message', $msg->rclMessage) }}" class="uk-button uk-button-small uk-button-primary">View</a>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('controllers.clx.delete-rcl-message', $msg->rclMessage) }}">
                                    @csrf
                            <button class="uk-button uk-button-small" onclick="return confirm('Are you sure?')">DEL</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script type="module">
        $(document).ready(function () {
            let table = new DataTable('#dataTable', {
                responsive: true,
                order: [[
                    7, 'desc'
                ]]
            });
        })

        $('#selectAll').click(function (e) {
            e.preventDefault();
            $(':checkbox').each(function () {
                this.checked = true;
            });
        })
    </script>
@endsection
