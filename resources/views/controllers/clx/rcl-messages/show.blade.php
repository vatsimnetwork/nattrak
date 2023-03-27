@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-left uk-padding-remove-right">
        <form method="POST" action="{{ route('controllers.clx.transmit', $message) }}" class="col-lg-12 bg-light rounded pt-3 pb-3">
            @csrf
            <a href="{{ route('controllers.clx.pending') }}"><i class="fas fa-angle-left"></i> Back</a>
            <h3>{{ $message->callsign }} RCL Message</h3>
            @if ($message->isEditLocked() && $message->editLockVatsimAccount != Auth::user())
                <div class="uk-alert uk-alert-warning">
                    <h6>{{ $message->editLockVatsimAccount->full_name }} {{ $message->editLockVatsimAccount->id }} is editing this as of {{ $message->edit_lock_time->diffForHumans() }}.</h6>
                </div>
            @endif
            @if ($errors->any())
                <div class="uk-alert uk-alert-danger" role="alert">
                    <h6>Some input was incorrect.</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="uk-grid uk-grid-small" uk-grid>
                <div class="{{ $message->clxMessages->count() > 0 ? 'uk-width-1-2' : '' }}">
                    <div>
                        <h5 style="margin-bottom: 0;">Request Data</h5>
                        <table id="dataTable" class="uk-table uk-table-small uk-table-striped uk-padding-remove uk-margin-remove uk-table-middle" style="width:100%;">
                            <thead>
                                <td class="uk-width-small"></td>
                                <th></th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>CS</td>
                                <td>{{ $message->callsign }}</td>
                            </tr>
                            <tr>
                                <td>DEST</td>
                                <td>{{ $message->destination }}
                            </tr>
                            <tr>
                                <td>TRACK/RR</td>
                                <td>{{ $message->track ? 'NAT '. $message->track->identifier . ' ' . $message->track->last_routeing : $message->random_routeing }}</td>
                            </tr>
                            <tr>
                                <td>ENTRY</td>
                                <td>{{ $message->entry_fix }}</td>
                            </tr>
                            <tr>
                                <td>ETA</td>
                                <td>{{ $message->entry_time }}</td>
                            </tr>
                            <tr>
                                <td>FL</td>
                                <td>{{ $message->flight_level }}</td>
                            </tr>
                            <tr>
                            @if ($message->is_concorde)
                                <td>UFL</td>
                                <td>{{ $message->upper_flight_level }}</td>
                            @else
                                <td>MFL</td>
                                <td>{{ $message->max_flight_level ?? 'N/A' }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td>MACH</td>
                                <td>{{ $message->mach }}</td>

                            </tr>
                            <tr>
                                <td>TIME</td>
                                <td>{{ $message->request_time->format('Hi') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h5 style="margin: 20px 0 0;">ATC Requirements (changes only)</h5>
                        <div class="uk-margin">
                            <div class="uk-form-horizontal">
                                <div class="uk-margin">
                                    <label class="uk-form-label" for="">Datalink authority</label>
                                    <div class="uk-form-controls">
                                        <select name="datalink_authority" id="" autocomplete="off" class="uk-select uk-form-small">
                                            @foreach($dlAuthorities as $authority)
                                                <option value="{{ $authority->value }}" @if($authority->value == $activeDlAuthority->value) selected="selected" @endif>{{ $authority->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if (!$message->is_concorde)
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="">Change flight level to</label>
                                        <div class="uk-form-controls">
                                            <select name="atc_fl" id="atc_fl" autocomplete="off" class="uk-select uk-form-small">
                                                <option value="" selected>Don't change</option>
                                                @for ($i = 200; $i <= 450; $i += 10)
                                                    @if (in_array($i, [420, 440])) @continue @endif
                                                    <option value="{{ $i }}">FL {{ $i }} @if ($message->flight_level == $i) (pilot request) @elseif ($message->max_flight_level == $i) (max pilot flight level) @endif</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="">Change mach to</label>
                                        <div class="uk-form-controls">
                                            <select name="atc_mach" id="atc_mach" autocomplete="off" class="uk-select uk-form-small">
                                                <option value="" selected>Don't change</option>
                                                @for ($i = 55; $i < 99; $i++)
                                                    <option value="0{{ $i }}">0{{ $i }} @if ($message->mach == '0' . $i) (pilot request) @endif</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <hr>
                                <div class="uk-margin">
                                    <label class="uk-form-label" for="">Entry time requirement for {{ $message->entry_fix }}</label>
                                    <div class="uk-form-controls">
                                        <select class="uk-select uk-form-small" autocomplete="off" name="entry_time_type" id="entry_time_type">
                                            <option value="=" selected>At</option>
                                            <option value="<">Before</option>
                                            <option value=">">After</option>
                                        </select>
                                        <input type="number" name="entry_time_requirement" id="entry_time_requirement" class="uk-input uk-form-small" value="{{ $message->entry_time }}" maxlength="4">
{{--                                        <script type="module">--}}
{{--                                            $('#entry_time_type').on('change', function () {--}}
{{--                                                if (this.value == '') {--}}
{{--                                                    $('#entry_time_requirement').hide();--}}
{{--                                                } else {--}}
{{--                                                    $('#entry_time_requirement').show();--}}
{{--                                                }--}}
{{--                                            });--}}
{{--                                        </script>--}}
                                    </div>
                                </div>
                                <hr>
                                <div class="uk-margin">
                                    <label class="uk-form-label" for="">Change route to another NAT</label>
                                    <div class="uk-form-controls">
                                        <select class="uk-select uk-form-small" autocomplete="off" name="new_track_id" id="new_track_id">
                                            <option value="" selected>None</option>
                                            @foreach($tracks as $track)
                                                <option value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="uk-margin">
                                    <label class="uk-form-label" for="">Change route to another RR</label>
                                    <div class="uk-form-controls">
                                        <input type="text" name="new_random_routeing" id="new_random_routeing" class="uk-input uk-form-small" autocomplete="off" placeholder="">
                                    </div>
                                </div>
                                <hr>
                                <div class="uk-margin">
                                    <label class="uk-form-label" for="">Free text</label>
                                    <div class="uk-form-controls">
                                        <input type="text" name="free_text" class="uk-input uk-form-small">
                                    </div>
                                </div>
                                <div class="uk-margin">
                                    <div class="uk-card uk-card-default uk-card-body uk-padding-remove" style="padding: 15px !important;">
                                        <livewire:controllers.conflict-checker callsign="{{ $message->callsign }}" level="{{ $message->flight_level }}" time="{{ $message->entry_time }}" entry="{{ $message->entry_fix }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="uk-text-meta">Pilot details: {{ $message->vatsimAccount->id }}</p>
                    <div class="form-inline">
                        <button class="uk-button uk-button-primary" onclick="" type="submit">Transmit {{ $message->clxMessages->count() > 0 ? 'Reclearance' : 'Clearance' }}</button>
                    </div>
                </div>
                @if ($message->clxMessages->count() > -1)
                    <div class="uk-width-1-2">
                        <div>
                            <p>
                                CLX Messages in reply to this RCL
                            </p>
                            @foreach($message->clxMessages->sortbyDesc('created_at') as $clx)
                                <div class="uk-card uk-card-default uk-card-body uk-margin" style="padding: 10px; box-shadow: none !important;">
                                    <p>{{ $clx->vatsimAccount->full_name }} {{ $clx->vatsimAccount->id }} - {{ $clx->created_at }}</p>
                                    <p>
                                        @foreach($clx->datalink_message as $line)
                                            {{ $line }}<br>
                                        @endforeach
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </form>
        <div class="uk-margin uk-grid" uk-grid>
            <form action="{{ route('controllers.clx.revert-to-voice', $message) }}" method="post">
                @csrf
                <button class="uk-button uk-button-small">Revert To Voice</button>
            </form>
            <form action="{{ route('controllers.clx.delete-rcl-message', $message) }}" method="post">
                @csrf
                <button class="uk-button uk-button-small" onclick="return confirm('Are you sure? Make sure to communicate with the pilot.')">Delete Request</button>
            </form>
        </div>
    </div>
    <script type="module">
        $("#atc_fl").change(function () {
            Livewire.emit('levelChanged', this.value);
        });

        $('#entry_time_requirement').blur(function () {
            Livewire.emit('timeChanged', this.value);
        });

        $('#new_track_id').change(function () {
            Livewire.emit('trackChanged', this.value);
        });

        $('#new_random_routeing').blur(function () {
            Livewire.emit('rrChanged', this.value);
        })
    </script>
@endsection
