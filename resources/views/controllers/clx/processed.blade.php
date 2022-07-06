@extends('_layouts.main')
@section('page')
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Processed RCL Messages
            </p>
            <p class="lead">{{ $displayedTrack ? 'Displaying track '. $displayedTrack->identifier : 'All tracks + random routeing clearances' }}</p>
            <form method="GET" action="{{ route('controllers.clx.processed') }}" class="form-inline">
                <label for="">Change track</label>
                <select name="sortByTrack" id="" class="custom-select custom-select-sm mx-3">
                    <option value="all">All tracks + random routeing clearances</option>
                    {{--                    <option value="rr">Random routeings</option>--}}
                    @foreach ($tracks as $track)
                        <option value="{{ $track->identifier }}">{{ $track->identifier }} ({{ $track->last_routeing }}
                            )
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary" type="submit">Sort</button>
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
                            <th>{{ $msg->callsign }}</th>
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
    </div>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
@endsection
