@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <form method="POST" action="{{ route('controllers.clx.transmit', $message) }}" class="col-lg-12 bg-light rounded pt-3 pb-3">
            @csrf
            <a href="{{ route('controllers.clx.pending') }}"><i class="fas fa-angle-left"></i> Back</a>
            <p class="header">{{ $message->callsign }} RCL Message</p>
            @if ($message->isEditLocked() && $message->editLockVatsimAccount != Auth::user())
                <div class="alert alert-danger">
                    <b>{{ $message->editLockVatsimAccount->full_name }} {{ $message->editLockVatsimAccount->id }} is editing this as of {{ $message->edit_lock_time->diffForHumans() }}.</b>
                </div>
            @endif
            <hr>
            @if ($message->clxMessages->count() > 0)
                <div>
                    <p>
                        CLX Messages in reply to this RCL
                    </p>
                    @foreach($message->clxMessages->sortbyDesc('created_at') as $clx)
                        <div class="p-3 border">
                            <p>{{ $clx->vatsimAccount->full_name }} {{ $clx->vatsimAccount->id }} - {{ $clx->created_at }}</p>
                            <p>
                                @foreach($clx->datalink_message as $line)
                                    {{ $line }}<br>
                                @endforeach
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
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
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <b>Some input was incorrect.</b>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="border p-3">
                    <div class="form-inline">
                        <label for="">Datalink authority</label>
                        <select name="datalink_authority" id="" autocomplete="off" class="custom-select custom-select-sm ml-2">
                            @foreach($dlAuthorities as $authority)
                                <option value="{{ $authority->value }}" @if($authority->value == $activeDlAuthority->value) selected="selected" @endif>{{ $authority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-inline mt-2">
                        <label for="">Change flight level to</label>
                        <select name="atc_fl" id="" autocomplete="off" class="custom-select custom-select-sm ml-2">
                            <option value="" selected>Don't change</option>
                            @for ($i = 200; $i <= 450; $i += 10)
                                @if (in_array($i, [420, 440])) @continue @endif
                                <option value="{{ $i }}">FL {{ $i }} @if ($message->flight_level == $i) (pilot request) @elseif ($message->max_flight_level == $i) (max pilot flight level) @endif</option>
                            @endfor
                        </select>
                        <label for="" class="ml-3">Change mach to</label>
                        <select name="atc_mach" id="" autocomplete="off" class="custom-select custom-select-sm ml-2">
                            <option value="" selected>Don't change</option>
                            @for ($i = 55; $i < 99; $i++)
                                <option value="0{{ $i }}">0{{ $i }} @if ($message->mach == '0' . $i) (pilot request) @endif</option>
                            @endfor
                        </select>
                    </div>
                    <hr>
                    <div class="form-inline mt-2">
                        <label for="">Entry time requirement for {{ $message->entry_fix }}</label>
                        <select class="custom-select custom-select-sm ml-2" autocomplete="off" name="entry_time_type" id="entry_time_type">
                            <option value="" selected>None</option>
                            <option value="<">Before</option>
                            <option value="=">At</option>
                            <option value=">">After</option>
                        </select>
                        <input style="display:none" type="number" name="entry_time_requirement" id="entry_time_requirement" class="form-control form-control-sm ml-2" placeholder="1015" maxlength="4">
                        <script>
                            $('#entry_time_type').on('change', function () {
                               if (this.value == '') {
                                   $('#entry_time_requirement').hide();
                               } else {
                                   $('#entry_time_requirement').show();
                               }
                            });
                        </script>
                    </div>
                    <hr>
                    <div class="form-inline mt-2">
                        <label for="">Change route to another NAT</label>
                        <select class="custom-select custom-select-sm ml-2" autocomplete="off" name="new_track_id">
                            <option value="" selected>None</option>
                            @foreach($tracks as $track)
                                <option value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                            @endforeach
                        </select>
                    </div><div class="form-inline mt-2">
                        <label for="">Change route to another RR</label>
                        <input type="text" name="new_random_routeing" class="form-control form-control-sm ml-2" autocomplete="off" placeholder="">
                    </div>
                    <hr>
                    <div class="form-inline mt-2">
                        <label for="">Free text</label>
                        <input type="text" name="free_text" class="form-control form-control-sm ml-2">
                    </div>
                </div>
            </div>
            <p class="my-2 small">Pilot details: {{ $message->vatsimAccount->id }}</p>
            <div class="form-inline">
                <button class="btn btn-primary mt-3" type="submit">Transmit {{ $message->clxMessages->count() > 0 ? 'Reclearance' : 'Clearance' }}</button>
            </div>
        </form>
        <form action="{{ route('controllers.clx.revert-to-voice', $message) }}" method="post">
            @csrf
            <button class="btn btn-sm mt-3 ml-2 text-white">Revert To Voice</button>
        </form>
        <form action="{{ route('controllers.clx.delete-rcl-message', $message) }}" method="post">
            @csrf
            <button class="btn btn-sm mt-3 ml-2 text-white" onclick="return confirm('Are you sure? Make sure to communicate with the pilot.')">Delete Request</button>
        </form>
    </div>
@endsection
