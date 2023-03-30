@extends('_layouts.main')
@section('page')
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css"/>
    <div class="uk-container uk-padding uk-padding-remove-left uk-padding-remove-right">
        <a href="{{ route('pilots.rcl.index') }}"><i class="fas fa-angle-left"></i> Back</a>
        <div class="uk-flex uk-flex-row uk-flex-between">
            <h1 class="uk-text-bold uk-text-primary" id="start">Request oceanic clearance</h1>
            <button onclick="startTour()" class="uk-button">Help</button>
        </div>
        @if ($errors->any())
            <div class="uk-alert uk-alert-danger" role="alert">
                <p class="uk-text-bold">Some input was incorrect.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <p>Need help? Press the <span class="uk-text-bold uk-text-small">HELP</span> button on the top right.</p>
            </div>
        @endif
        @if ($isConcorde)
            <div class="uk-alert uk-alert-primary" role="alert">
                Concorde aircraft type detected.
            </div>
        @endif
        <form class="uk-form-stacked" action="{{ route('pilots.rcl.store') }}" method="post">
            @csrf
            <h4>Flight information</h4>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-2@m">
                    <label for="callsign" class="uk-form-label">Callsign</label>
                    <div class="uk-form-controls">
                        <input maxlength="7" required type="text" class="uk-input" name="callsign" id="callsign" placeholder="Enter callsign" value="{{ $callsign ?? old('callsign') }}" onblur="this.value = this.value.toUpperCase()">
                        @if ($callsign)
                            <small class="uk-text-meta">Your callsign was automatically collected. You may change the callsign if it is incorrect.</small>
                        @endif
                    </div>
                </div>
                <div class="uk-width-1-2@m">
                    <label for="destination" class="uk-form-label">Destination ICAO</label>
                    <div class="uk-form-controls">
                        <input required type="text" class="uk-input" name="destination" id="destination" placeholder="Enter destination ICAO (e.g. EGLL)" maxlength="4" value="{{ $arrival_icao ?? old('destination') }}" onblur="this.value = this.value.toUpperCase()">
                        @if (!$arrival_icao)
                            <small class="uk-text-meta">Your destination was automatically collected. You may change the destination if it is incorrect.</small>
                        @endif
                    </div>
                </div>
                <div class="uk-width-1-2@m">
                    <label for="flight_level" class="uk-form-label">Requested {{ $isConcorde ? 'lower block' : '' }} flight level</label>
                    <div class="uk-form-controls">
                        <input required type="text" class="uk-input" name="flight_level" id="flight_level" placeholder="e.g. 310" maxlength="3" value="{{ $flight_level ?? old('flight_level') }}">
                        @if (config('app.ctp_info_enabled'))
                            <small class="uk-text-meta"><b>Ensure you enter your assigned oceanic flight level as per your booking!</b></small>
                        @endif
                        @if ($flight_level)
                            <small class="uk-text-meta">Your requested flight level (the altitude on your flight plan) was automatically collected. You may change the flight level if it is incorrect.</small>
                        @endif
                    </div>
                </div>
                @if ($isConcorde)
                    <div class="uk-width-1-2@m">
                        <label for="flight_level" class="uk-form-label">Requested upper block flight level</label>
                        <div class="uk-form-controls">
                            <input required type="text" class="uk-input" name="upper_flight_level" id="upper_flight_level" placeholder="e.g. 310" maxlength="3" value="{{ old('upper_flight_level') }}">
                        </div>
                    </div>
                @else
                    <div class="uk-width-1-2@m">
                        <label for="max_flight_level" class="uk-form-label">Maximum flight level</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input" name="max_flight_level" id="max_flight_level" placeholder="e.g. 390" maxlength="3" value="{{ old('max_flight_level') }}">
                            @if (config('app.ctp_info_enabled'))
                                <small class="uk-text-meta"><b>Ensure you enter your max flight level as per your booking!</b></small>
                            @endif
                            <small class="uk-text-meta">This is the highest flight level you can accept.</small>
                        </div>
                    </div>
                @endif
                <div class="uk-width-1-2@m">
                    <label for="mach" class="uk-form-label">Requested mach number</label>
                    <div class="uk-form-controls">
                        <input required type="text" class="uk-input" name="mach" id="mach" placeholder="e.g. 080" maxlength="3" value="{{ old('mach') }}">
                        <small class="uk-text-meta">Your requested mach number (don't include the dot at the start)</small>
                    </div>
                </div>
            </div>
            <h4>Route</h4>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-2-5@m">
                    <label for="track_id" class="uk-form-label">Requested NAT Track</label>
                    <div class="uk-form-controls">
                        <select class="uk-select" id="track_id" name="track_id">
                            <option value="" selected>None</option>
                            @foreach($tracks as $track)
                                <option data-routeing="{{ $track->last_routeing }}" value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="uk-width-expand@m">
                    <div class="uk-flex uk-flex-middle uk-flex-column">
                        <div class="uk-text-center uk-text-italic">or...</div>
                    </div>
                </div>
                <div class="uk-width-2-5@m">
                    <label for="random_routeing" class="uk-form-label">Requested random routeing</label>
                    <div class="uk-form-controls">
                        <input value="{{ old('random_routeing') }}" type="text" class="uk-input" name="random_routeing" id="random_routeing" placeholder="e.g. GOMUP 59/20 59/30 58/40 56/50 JANJO" onblur="this.value = this.value.toUpperCase()">
                    </div>
                </div>
            </div>
            <h4>Oceanic entry</h4>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-2@m">
                    <label for="entry_fix" class="uk-form-label">Entry fix</label>
                    <div class="uk-form-controls">
                        <input value="{{ old('entry_fix') }}" required type="text" class="uk-input" name="entry_fix" id="entry_fix" placeholder="e.g. MALOT" maxlength="7" onblur="this.value = this.value.toUpperCase()">
                        <small class="uk-text-meta">The first fix/waypoint in oceanic airspace.</small>
                        <br/>
                        <small class="uk-text-meta uk-text-bold" style="display: none;" id="oep-autofilled-msg">This fix was auto-filled, based on your selected track..</small>
                    </div>
                </div>
                <div class="uk-width-1-2@m">
                    <label for="entry_time" class="uk-form-label">Estimated time of arrival for entry fix</label>
                    <div class="uk-form-controls">
                        <input value="{{ old('entry_time') }}" required type="number" class="uk-input" name="entry_time" id="entry_time" placeholder="e.g. 1350">
                        <small class="uk-text-meta">You can find this in your FMC, providing your simulator is set to real time.</small>
                        <a class="uk-link-text uk-text-meta" target="_blank" href="https://knowledgebase.ganderoceanic.ca/nattrak/requesting-oceanic-clearance/#section-3-oceanic-entry">An example is available here.</a>
                    </div>
                </div>
            </div>
            <h4>Metadata</h4>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-2@m">
                    <label for="tmi" class="uk-form-label">Current TMI (available at top of page)</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-input" value="{{ old('tmi') }}" required name="tmi" id="tmi" placeholder="e.g. 090" maxlength="4">
                    </div>
                </div>
                <div class="uk-width-1-2@m">
                    <label for="free_text" class="uk-form-label">Free text</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-input" value="{{ old('free_text') }}" name="free_text" id="free_text">
                    </div>
                </div>
                <div class="uk-form-controls">
                    <button type="submit" class="uk-button uk-button-primary">Submit Oceanic Clearance Request</button>
                </div>
            </div>
            @if ($isConcorde)
                <input type="hidden" name="is_concorde" value="1">
            @else
                <input type="hidden" name="is_concorde" value="0">
            @endif
        </form>
    </div>
    <script type="module">
        $("#track_id").change(function () {
            if (this.value == '') {
                $("#entry_fix").prop('disabled', false).val('');
                $("#oep-autofilled-msg").hide();
                return;
            }
            const routeing = $(this).find(':selected').data("routeing");
            if (routeing == '' || routeing == null) {
                return;
            }
            $("#entry_fix").prop('disabled', true).val(routeing.replace(/ .*/,''));
            $("#oep-autofilled-msg").show();
        });
    </script>
    <script>
        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                scrollTo: true
            }
        });

        const nextButton = {
            text: 'Next step',
            action: tour.next
        }

        const cancelButton = {
            text: 'Cancel tour',
            action: tour.cancel
        }

        const addTourStep = (id, text) => {
            tour.addStep({
                id: id,
                text: text,
                attachTo: {
                    element: `#${id}`,
                    on: 'bottom'
                },
                buttons: [ cancelButton, nextButton ]
            })
        }

        addTourStep('start', 'This tour will guide you through each step of submitting your oceanic clearance request. Click Next step to continue');
        addTourStep('callsign', 'Enter the callsign of your flight. For example: BAW21A, DLH625, AAL912. This needs to the same as the callsign you\'ve signed into vPilot/xPilot/Swift as.');
        addTourStep('destination', 'Enter the 4 letter ICAO code of your destination airport. You can find this in your flight plan. For example, EGLL for London Heathrow.');
        addTourStep('flight_level', 'Enter the flight level you wish to fly on the crossing. Generally this will be your current cruise level, or you could step climb prior to entry. Enter it as 3 digits e.g. 390, without the FL prefix.');
        addTourStep('max_flight_level', 'Enter the maximum flight level you can fly during the crossing. The controller will use this information in case they need to change your flight level during the crossing. You can find this information in your aircraft\'s FMC/MCDU under the PROG (Airbus) or VNAV (Boeing) pages.');
        addTourStep('mach', 'Enter the Mach number/speed you wish to fly on the crossing. Enter it as 3 digits e.g. 080 (.80 becomes 080), without the M prefix. If you need help with Mach numbers, check out the full Tutorial on the navigation bar.');
        addTourStep('track_id', 'If you\'re flying a track, select it here. You can find it in your flight plan either directly or by cross referencing the track route to your flight plan. If you\'re not flying a track, leave this blank and enter a Random Routeing.');
        addTourStep('random_routeing', 'If you\'re flying a random routeing (not a track), enter it here. It is the part of your flight plan route from oceanic entry to oceanic exit. Your flight plan OFP from SimBrief or similar services can help you figure out the entry and exit.');
        addTourStep('entry_fix', 'Enter the entry fix/waypoint of your oceanic crossing here. Most of the time it is the first fix on the track or random routeing you selected.')
        addTourStep('entry_time', 'Enter the estimated time of arrival to the entry fix in real Zulu/GMT time. You can find this in your FMC or flight plan. You may need to bring your sim back to real time to get an accurate reading. If you need further help, check out the full Tutorial on the navigation bar.');
        addTourStep('tmi', 'Enter the current TMI, which you can find on the top of the page. This identifier confirms you\'re in receipt of up to date crossing information.')
        addTourStep('free_text', 'If you have any extra information or requests for the controller, enter them here. Otherwise leave this blank.');

        function startTour() {
            tour.start();
        }
    </script>
@endsection
