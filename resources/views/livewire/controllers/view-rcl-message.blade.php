<div>
    <h5 class="text-secondary font-display">Request Message</h5>
    <h3 class="font-display">{{ $rclMessage->callsign }} to {{ $rclMessage->destination }} {{ $rclMessage->is_concorde ? '(Concorde)' : '' }}</h3>
    @if ($rclMessage->isEditLocked() && $rclMessage->editLockVatsimAccount != Auth::user())
        <div class="alert alert-warning">
            <span>{{ $rclMessage->editLockVatsimAccount->full_name }} {{ $rclMessage->editLockVatsimAccount->id }} is editing this as of {{ $rclMessage->edit_lock_time->diffForHumans() }}.</span>
        </div>
    @endif
    @if ($rclMessage->re_request)
        <div class="alert alert-warning">
            <span>This is a re-request.</span>
        </div>
    @endif
    @if ($rclMessage->is_acknowledged)
        RCL has been auto acknowledged.
    @endif
    <div class="accordion" id="accordionPanelsStayOpenExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                    Request
                </button>
            </h2>
            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Callsign</label>
                            <input type="text" value="{{ $rclMessage->callsign }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Destination</label>
                            <input type="text" value="{{ $rclMessage->destination }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label-sm">Track/RR</label>
                            <input type="text" value="{{ $rclMessage->track ? 'NAT '. $rclMessage->track->identifier . ' ' . $rclMessage->track->last_routeing : $rclMessage->random_routeing }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Entry Fix</label>
                            <input type="text" value="{{ $rclMessage->entry_fix }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Entry ETA</label>
                            @if ($rclMessage->new_entry_time)
                                <div class="mt-1">
                                    <span class="badge rounded-pill text-bg-primary" style="font-size: 13px">{{ $rclMessage->entry_time }}</span> - <span class="fst-italic">prev {{ $rclMessage->previous_entry_time }} - notified at {{ $rclMessage->new_entry_time_notified_at->format('Hi') }}</span>
                                </div>
                            @else
                                <div class="mt-1">{{ $rclMessage->entry_time }}</div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Entry FL</label>
                            <input type="text" value="{{ $rclMessage->flight_level }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        @if ($rclMessage->is_concorde)
                            <div class="col-md-3">
                                <label class="col-form-label-sm">Upper FL</label>
                                <input type="text" value="{{ $rclMessage->upper_flight_level }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                            </div>
                        @else
                            <div class="col-md-3">
                                <label class="col-form-label-sm">Max FL</label>
                                <input type="text" value="{{ $rclMessage->max_flight_level ?? 'N/A' }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Entry Mach</label>
                            <input type="text" value="{{ $rclMessage->mach }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Requested At</label>
                            <input type="text" value="{{ $rclMessage->request_time->format('Hi') }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">CID</label>
                            <input type="text" value="{{ $rclMessage->vatsimAccount->id }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label-sm">Target OCA</label>
                            <input type="text" value="{{ $rclMessage->targetDatalinkAuthority->id }}" class="form-control-plaintext form-control-plaintext-sm" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $rclMessage->clxMessages->isNotEmpty() ? 'collapsed' : ''}}" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                    ATC Clearance
                </button>
            </h2>
            <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse {{ $rclMessage->clxMessages->isNotEmpty() ? '' : 'show'}}">
                <div class="accordion-body mb-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col">
                                    <label class="form-label" for="">Datalink authority</label>
                                    <div class="">
                                        <select wire:model="atcDatalinkAuthority" name="datalink_authority" id="" autocomplete="off" class="form-select form-select-sm">
                                            @foreach($datalinkAuthorities as $authority)
                                                <option value="{{ $authority->id }}">{{ $authority->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="">Change {{ $rclMessage->is_concorde ? 'lower block' : '' }} flight level to</label>
                                    <div class="uk-form-controls">
                                        <select wire:model="atcFlightLevel" name="atc_fl" id="atc_fl" autocomplete="off" class="form-select form-select-sm">
                                            <option value="" selected>Don't change</option>
                                            @for ($i = 200; $i <= 600; $i += 10)
                                                @if (in_array($i, [420, 440])) @continue @endif
                                                <option value="{{ $i }}">FL {{ $i }} @if ($rclMessage->flight_level == $i) (pilot request) @elseif ($rclMessage->max_flight_level == $i) (max pilot flight level) @endif</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                @if (!$rclMessage->is_concorde)
                                    <div class="col">
                                        <label class="form-label" for="">Change mach to</label>
                                        <div class="uk-form-controls">
                                            <select wire:model="atcMach" name="atc_mach" id="atc_mach" autocomplete="off" class="form-select form-select-sm">
                                                <option value="" selected>Don't change</option>
                                                @for ($i = 55; $i < 99; $i++)
                                                    <option value="0{{ $i }}">0{{ $i }} @if ($rclMessage->mach == '0' . $i) (pilot request) @endif</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col">
                                        <label class="form-label" for="">Change upper block flight level to</label>
                                        <div class="uk-form-controls">
                                            <select wire:model="atcUpperFlightLevel" name="atc_ufl" id="atc_ufl" autocomplete="off" class="form-select form-select-sm">
                                                <option value="" selected>Don't change</option>
                                                @for ($i = 200; $i <= 600; $i += 10)
                                                    @if (in_array($i, [420, 440])) @continue @endif
                                                    <option value="{{ $i }}">FL {{ $i }} @if ($rclMessage->upper_flight_level == $i) (pilot request)@endif</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <hr class="my-3">
                                <div class="col">
                                    <label for="cto_time" class="form-label">Cleared time over for {{ $rclMessage->entry_fix }}</label>
                                    <div class="input-group">
                                        <input wire:model="atcCtoTime" required type="number" class="form-control" value="{{ $rclMessage->entry_time }}" name="cto_time" id="cto_time" placeholder="e.g. 1350">
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="col">
                                    <label class="form-label" for="entry_time_type">Entry restriction for {{ $rclMessage->entry_fix }}</label>
                                    <div class="input-group">
                                        <select wire:model="atcEntryTimeType" class="form-select form-select-sm" autocomplete="off" name="entry_time_type" id="entry_time_type">
                                            <option value="none" selected>None</option>
                                            <option value="=">At</option>
                                            <option value="<">Before</option>
                                            <option value=">">After</option>
                                        </select>
                                        <input wire:model="atcEntryTimeRequirement" type="number" name="entry_time_requirement" id="entry_time_requirement" class="form-control form-conrtol-sm" value="{{ $rclMessage->entry_time }}" maxlength="4">
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="col">
                                    <label class="form-label" for="">Change route to another NAT</label>
                                    <div >
                                        <select wire:model="atcNewTrack" class="form-select form-select-sm" autocomplete="off" name="new_track_id" id="new_track_id">
                                            <option value="" selected>None</option>
                                            @foreach($tracks as $track)
                                                <option value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="">Change route to another RR</label>
                                    <div class="uk-form-controls">
                                        <input wire:model="atcNewRandomRouteing" type="text" name="new_random_routeing" id="new_random_routeing" class="form-control form-conrtol-sm" autocomplete="off" placeholder="">
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="col">
                                    <label class="form-label" for="">Free text</label>
                                    <div class="uk-form-controls">
                                        <input wire:model="atcFreeText" type="text" name="free_text" class="form-control form-conrtol-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body" style="padding: 15px !important;">
                                <livewire:controllers.conflict-checker callsign="{{ $rclMessage->callsign }}" level="{{ $rclMessage->flight_level }}" time="{{ $rclMessage->entry_time }}" entry="{{ $rclMessage->entry_fix }}"/>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-success" wire:click.prevent="transmitClearance" onclick="" type="submit">Transmit {{ $rclMessage->clxMessages->count() > 0 || $rclMessage->is_acknowledged ? 'Reclearance' : 'Clearance' }}</button>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Other actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" wire:click.prevent="revertToVoice" wire:confirm="Are you sure you want to tell the pilot to revert to voice?" href="#">Revert to voice</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#declineDeleteModal" href="#">Decline and delete</a></li>
                                        @if ($rclMessage->is_acknowledged)
                                        <li><a class="dropdown-item" wire:click.prevent="moveToProcessedList" wire:confirm="Are you sure? This will send an auto acknowledge clearance message to the pilot." href="#">Move to processed list</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <p>Some input was incorrect.</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $rclMessage->clxMessages->isEmpty() ? 'collapsed' : ''}}" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                    <span>Previous ATC Clearances</span>
                    @if ($rclMessage->clxMessages->isNotEmpty())
                        <div>
                            <span class="badge rounded-pill text-bg-primary" style="font-size: 13px">{{ $rclMessage->clxMessages->count() }}</span>
                        </div>
                    @endif
                </button>
            </h2>
            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse {{ $rclMessage->clxMessages->isNotEmpty() ? 'show' : ''}}">
                <div class="accordion-body">
                    @if ($rclMessage->previous_clx_message)
                        <div class="card">
                            <div class="card-body">
                                <p class="text-secondary">Prior to ETA Notify - Issued by {{ $rclMessage->previous_clx_message['vatsim_account_id'] }} - {{ $rclMessage->previous_clx_message['created_at'] }}</p>
                                <p>
                                    @foreach($rclMessage->previous_clx_message['datalink_message'] as $line)
                                        {{ $line }}<br>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    @endif
                    @foreach($rclMessage->clxMessages->sortbyDesc('created_at') as $clx)
                        <div class="card">
                            <div class="card-body">
                                <p class="text-secondary">Issued by {{ $clx->vatsimAccount->full_name }} {{ $clx->vatsimAccount->id }} - {{ $clx->created_at }} ({{ $clx->created_at->diffForHumans() }})</p>
                                <p>
                                    @foreach($clx->datalink_message as $line)
                                        {{ $line }}<br>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    @endforeach
                    @if ($rclMessage->clxMessages->isEmpty())
                        <p>None issued.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="declineDeleteModal" tabindex="-1" aria-labelledby="declineDeleteModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Decline and delete</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input wire:model="declineDeleteReason" placeholder="Enter a reason here..." class="form-control"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" wire:click.prevent="declineAndDelete" class="btn btn-danger">Decline and delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
