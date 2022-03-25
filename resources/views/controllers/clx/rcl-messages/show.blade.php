@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <form method="POST" action="{{ route('controllers.clx.transmit', $message) }}" class="col-lg-12 bg-light rounded pt-3 pb-3">
            @csrf
            <a href="{{ route('controllers.clx.pending') }}"><i class="fas fa-angle-left"></i> Back</a>
            <p class="header">{{ $message->callsign }} RCL Message</p>
            <hr>
            <div>
                <p>Request Data</p>
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
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>{{ $message->callsign }}</th>
                        <td>{{ $message->destination }}
                        <td>{{ $message->track ? 'NAT '. $message->track->identifier . ' ' . $message->track->last_routeing : $message->random_routeing }}</td>
                        <td>{{ $message->entry_fix }}</td>
                        <td>{{ $message->entry_time }}</td>
                        <td>{{ $message->flight_level }}</td>
                        <td>{{ $message->max_flight_level }}</td>
                        <td>{{ $message->mach }}</td>
                        <td>{{ $message->request_time->format('Hi') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <p>ATC Requirements (only change what you need!)</p>
                <div class="border p-3">
                    <div class="form-inline">
                        <label for="">Datalink authority</label>
                        <select name="datalink_authority" id="" class="custom-select custom-select-sm ml-2">
                            @foreach($dlAuthorities as $authority)
                                <option value="{{ $authority->value }}">{{ $authority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-inline mt-2">
                        <label for="">Change flight level to</label>
                        <select name="atc_fl" id="" class="custom-select custom-select-sm ml-2">
                            <option value="" selected>Don't change</option>
                            @for ($i = 200; $i < 460; $i += 10)
                                <option value="{{ $i }}">FL {{ $i }} @if ($message->flight_level == $i) (pilot request) @elseif ($message->max_flight_level == $i) (max pilot flight level) @endif</option>
                            @endfor
                        </select>
                        <label for="" class="ml-3">Change mach to</label>
                        <select name="atc_mach" id="" class="custom-select custom-select-sm ml-2">
                            <option value="" selected>Don't change</option>
                            @for ($i = 70; $i < 99; $i++)
                                <option value="0{{ $i }}">0{{ $i }} @if ($message->mach == '0' . $i) (pilot request) @endif</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-inline mt-2">
                        <label for="">Entry time requirement for {{ $message->entry_fix }}</label>
                        <input type="text" name="entry_time_requirement" class="form-control form-control-sm ml-2" placeholder="NOT BEFORE 1015">
                    </div>
                    <div class="form-inline mt-2">
                        <label for="">Free text</label>
                        <input type="text" name="free_text" class="form-control form-control-sm ml-2">
                    </div>
                </div>
            </div>
            <p class="my-2 small">Pilot details: {{ $message->vatsimAccount->id }}</p>
            <div class="form-inline">
                <button class="btn btn-primary mt-3" type="submit">Transmit Clearance</button>
                <button class="btn btn-sm mt-3 ml-2" onclick="return confirm('Are you sure? Make sure to communicate with the pilot.')" type="submit">Delete</button>
            </div>
        </form>
    </div>
@endsection
