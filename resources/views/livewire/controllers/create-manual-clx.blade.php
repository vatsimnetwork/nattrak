<div>
    <div class="container">
        <form wire:submit="submit">
            <p class="mb-1">
                <a class="icon-link icon-link-hover" href="{{ route('controllers.clx.pending') }}">
                    <i class="fa-solid fa-chevron-left"></i>
                    Pending
                </a>
            </p>
            <p class="mb-4">
                <a class="icon-link icon-link-hover" href="{{ route('controllers.clx.pending') }}">
                    <i class="fa-solid fa-chevron-left"></i>
                    Processed
                </a>
            </p>
            <h5 class="text-secondary font-display">Create Manual Clearance</h5>
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
            {{--            <div class="form-check">--}}
            {{--                <input class="form-check-input" type="checkbox" wire:model.blur="isConcorde" value="" id="concordeCheck">--}}
            {{--                <label class="form-check-label" for="concordeCheck">--}}
            {{--                    Concorde--}}
            {{--                </label>--}}
            {{--            </div>--}}
            <p>Concorde not supported currently.</p>
            <div class="row mt-4">
                <div class="col">
                    <h5 class="font-display">Flight information</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input wire:model.blur="callsign" maxlength="7" required type="text" class="form-control" name="callsign" id="callsign" placeholder="Enter callsign" value="{{ old('callsign') }}" onblur="this.value = this.value.toUpperCase()">
                                <label for="callsign" class="uk-form-label">Callsign</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input wire:model.blur="destination" required type="text" class="form-control" name="destination" id="destination" placeholder="Enter destination ICAO (e.g. EGLL)" maxlength="4" value="{{ old('destination') }}" onblur="this.value = this.value.toUpperCase()">
                                <label for="destination">Destination ICAO</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input wire:model.blur="flightLevel" required type="text" class="form-control" name="flight_level" id="flight_level" placeholder="e.g. 310" maxlength="3" value="{{ old('flight_level') }}">
                                <label for="flight_level">{{ $isConcorde ? 'Lower block flight' : 'Flight' }} level</label>
                            </div>
                        </div>
                        {{--                        @if ($isConcorde)--}}
                        {{--                            <div class="col-md-6">--}}
                        {{--                                <div class="form-floating">--}}
                        {{--                                    <input wire:model.blur="upperFlightLevel" required type="text" class="form-control" name="upper_flight_level" id="upper_flight_level" placeholder="e.g. 310" maxlength="3" value="{{ old('upper_flight_level') }}">--}}
                        {{--                                    <label for="flight_level">Upper block flight level</label>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        @else--}}
                        {{--                            <div class="col-md-6">--}}
                        {{--                                <div class="form-floating">--}}
                        {{--                                    <input wire:model.blur="maxFlightLevel" type="text" class="form-control" name="max_flight_level" id="max_flight_level" placeholder="e.g. 390" maxlength="3" value="{{ old('max_flight_level') }}">--}}
                        {{--                                    <label for="max_flight_level" class="uk-form-label">Maximum flight level</label>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        @endif--}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input wire:model.blur="mach" required type="text" class="form-control" name="mach" id="mach" placeholder="e.g. 080" maxlength="3" value="{{ old('mach') }}">
                                <label for="mach" class="uk-form-label">Mach</label>
                            </div>
                        </div>
                    </div>
                    <h5 class="font-display">Route</h5>
                    <div class="row gap-4 mb-4">
                        <div class="col-auto">
                            <div class="form-floating">
                                <select wire:model.blur="selectedTrack" class="form-select" id="track_id" name="track_id">
                                    <option value="" selected>None</option>
                                    @foreach($tracks as $track)
                                        <option data-routeing="{{ $track->last_routeing }}" value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                                    @endforeach
                                </select>
                                <label for="track_id">Track</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex justify-content-center">
                                <div class="fst-italic">or...</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-floating">
                                <input wire:model.blur="randomRouteing" value="{{ old('random_routeing') }}" type="text" class="form-control" name="random_routeing" id="random_routeing" placeholder="e.g. GOMUP 59/20 59/30 58/40 56/50 JANJO" onblur="this.value = this.value.toUpperCase()">
                                <label for="random_routeing">Random routeing</label>
                            </div>
                        </div>
                    </div>
                    <h5 class="font-display">Oceanic entry</h5>
                    <div class="row mb-5">
                        <div class="col-sm">
                            <div class="form-floating">
                                <input wire:model.blur="entryFix" value="{{ old('entry_fix') }}" required type="text" class="form-control" name="entry_fix" id="entry_fix" placeholder="e.g. MALOT" maxlength="7" onblur="this.value = this.value.toUpperCase()">
                                <label for="entry_fix" class="uk-form-label">Entry fix</label>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="form-floating">
                                <input wire:model.blur="entryTime" value="{{ old('entry_time') }}" required type="number" class="form-control" name="entry_time" id="entry_time" placeholder="e.g. 1350">
                                <label for="entry_time" class="uk-form-label">Entry fix time of arrival</label>
                            </div>
                        </div>
                    </div>
                    <h5 class="font-display">Metadata</h5>
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input wire:model.blur="tmi" type="text" class="form-control" value="{{ current_tmi() }}" required name="tmi" id="tmi" placeholder="e.g. 090" maxlength="4">
                                <label for="tmi" class="uk-form-label">TMI</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input wire:model.blur="freeText" type="text" class="form-control" value="{{ old('free_text') }}" name="free_text" id="free_text">
                                <label for="free_text" class="uk-form-label">Free text</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        @if ($callsign && $flightLevel && $entryFix && $entryTime)
                            <div class="card card-body" style="padding: 15px !important;">
                                <livewire:controllers.conflict-checker :callsign="$callsign" :level="$flightLevel" :time="$entryTime" :entry="$entryFix" wire:key="conflict-checker"/>
                            </div>
                        @else
                            <div class="card card-body" style="padding: 15px !important;">Enter clearance details to view conflicts</div>
                        @endif
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-success btn-lg">Create</button>
                    </div>
                    @if ($isConcorde)
                        <input type="hidden" id="is_concorde" name="is_concorde" value="1">
                    @else
                        <input type="hidden" id="is_concorde" name="is_concorde" value="0">
                    @endif
                </div>
            </div>
        </form>
    </div>
    <script type="module">
        $("#flight_level").change(function () {
            Livewire.dispatch('levelChanged', { newLevel: this.value });
        });

        $('#entry_time').blur(function () {
            Livewire.dispatch('timeChanged', { newTime: this.value });
        });

        $('#track_id').change(function () {
            Livewire.dispatch('trackChanged', { newTrackId: this.value });
        });

        $('#random_routeing').blur(function () {
            Livewire.dispatch('rrChanged', { newRouteing: this.value });
        });
    </script>
</div>
