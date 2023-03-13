@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-left uk-padding-remove-right">
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
                <button class="uk-button uk-button-small" type="submit">Sort</button>
            </div>
        </form>
        <hr>
        @if ($processedRclMsgs->count() == 0)
            No processed RCL messages.
        @endif
        <div>
            <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
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
                @foreach($processedRclMsgs as $msg)
                    <tr>
                        <th>{{ $msg->callsign }} {{ $msg->is_concorde ? '(CONC)' : '' }}</th>
                        <td>{{ $msg->destination }}
                        <td>{{ $msg->latestClxMessage?->track ? 'NAT '. $msg->latestClxMessage?->track->identifier : 'RR' }} {{ $msg->latestClxMessage?->routeing_changed ? '*' : ''}}</td>
                        <td>{{ $msg->latestClxMessage?->entry_fix }}</td>
                        <td>{{ $msg->latestClxMessage?->entry_time_restriction ? $msg->latestClxMessage?->entry_time_restriction . '*' : $msg->entry_time }}</td>
                        <td>{{ $msg->latestClxMessage?->flight_level }}{{ $msg->latestClxMessage?->flight_level != $msg->flight_level ? '*' : ''}}</td>
                        <td>{{ $msg->latestClxMessage?->mach }}{{ $msg->latestClxMessage?->mach != $msg->mach ? '*' : ''}}</td>
                        <td>{{ $msg->latestClxMessage->created_at->format('Hi') }}</td>
                        <td>
                            <a href="{{ route('controllers.clx.show-rcl-message', $msg) }}"
                               class="btn btn-sm btn-primary"><b>View</b></a>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('controllers.clx.delete-rcl-message', $msg) }}">
                                @csrf
                                <button class="btn btn-sm" onclick="return confirm('Are you sure?')">DEL</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
@endsection
