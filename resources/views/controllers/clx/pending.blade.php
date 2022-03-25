@extends('_layouts.main')
@section('page')
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Pending RCL Messages
            </p>
            <p class="lead">{{ $displayedTrack ? 'Displaying track '. $displayedTrack->identifier : 'All tracks + random routeing requests' }}</p>
            <form method="GET" action="{{ route('controllers.clx.pending') }}" class="form-inline">
                <label for="">Change track</label>
                <select name="sortByTrack" id="" class="custom-select custom-select-sm mx-3">
                    <option value="all">All tracks + random routeing requests</option>
{{--                    <option value="rr">Random routeings</option>--}}
                    @foreach ($tracks as $track)
                        <option value="{{ $track->identifier }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary" type="submit">Sort</button>
            </form>
            <hr>
            @if ($pendingRclMsgs->count() == 0)
                No pending RCL messages.
            @endif
            <div>
                <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>CS</th>
                            <th>DEST</th>
                            <th>ROUTE</th>
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
                                <th>{{ $msg->callsign }}</th>
                                <td>{{ $msg->destination }}
                                <td>{{ $msg->track ? 'NAT '. $msg->track->identifier : 'RR' }}</td>
                                <td>{{ $msg->entry_fix }}</td>
                                <td>{{ $msg->entry_time }}</td>
                                <td>{{ $msg->flight_level }}</td>
                                <td>{{ $msg->max_flight_level }}</td>
                                <td>{{ $msg->mach }}</td>
                                <td>{{ $msg->request_time->format('Hi') }}</td>
                                <td>
                                    <a href="{{ route('controllers.clx.show-rcl-message', $msg) }}" class="btn btn-sm btn-primary"><b>ACTION</b></a>
                                </td>
                                <td>
                                    <form action="">
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
        $(document).ready( function () {
            $('#dataTable').DataTable();
        } );
    </script>
@endsection
