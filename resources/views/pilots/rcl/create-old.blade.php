@extends('_layouts.main')
@section('page')
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css"/>
    <div class="container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Pilots</li>
                <li class="breadcrumb-item"><a href="{{ route('pilots.rcl.index') }}">Request Clearance</a></li>
                <li class="breadcrumb-item active" aria-current="page">Form</li>
            </ol>
        </nav>
        <div class="d-flex flex-row justify-content-between mt-4 mb-2">
            <h1 class="fs-2 text-primary-emphasis font-display" id="start">Request oceanic clearance</h1>
            <button onclick="startTour()" class="btn btn-outline-primary">Help</button>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p class="fw-bold">Some input was incorrect.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <p>Need help? Press the <span class="uk-text-bold uk-text-small">HELP</span> button on the top right.</p>
            </div>
        @endif
        <div class="bg-body-secondary py-3 px-3 mb-4 rounded">
            <p>You should request oceanic clearance <span class="fw-bold">30 minutes prior to oceanic entry.</span><br><a href="#" data-bs-toggle="modal" data-bs-target="#airportInfo" class="fst-italic text-muted">At some airports, you must request prior to departure.</a></p>
            <p>Requests will be rejected if your ETA has past, is under {{ (int)config('app.rcl_lower_limit') + 1 }} minutes from now, or is over {{ (int)config('app.rcl_upper_limit') - 1 }} minutes away.</p>
            <p class="mb-0">Need help? Check out the <i>Help</i> button in the navigation bar.</p>
        </div>
        @if (config('app.ctp_info_enabled'))
            <p><b>Please note that if you are flying across the oceanic without a CTP slot, you will be delayed and likely asked to move outside of the vertical limits of oceanic airspace.</b></p>
        @endif
        @if ($isConcorde)
            <div class="alert alert-info" role="alert">
                Concorde aircraft type detected.
            </div>
        @endif
        <form action="{{ route('pilots.rcl.store') }}" method="post">
            @csrf

            <h5 class="font-display">Flight information</h5>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input maxlength="7" required type="text" class="form-control" name="callsign" id="callsign" placeholder="Enter callsign" value="{{ $callsign ?? old('callsign') }}" onblur="this.value = this.value.toUpperCase()">
                        <label for="callsign" class="uk-form-label">Callsign</label>
                    </div>
                    @if (!$callsign)
                        <div class="form-text">Your callsign was automatically collected. You may change the callsign if it is incorrect.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input required type="text" class="form-control" name="destination" id="destination" placeholder="Enter destination ICAO (e.g. EGLL)" maxlength="4" value="{{ $arrival_icao ?? old('destination') }}" onblur="this.value = this.value.toUpperCase()">
                        <label for="destination">Destination ICAO</label>
                    </div>
                    @if (!$arrival_icao)
                        <div class="form-text">Your destination was automatically collected. You may change the destination if it is incorrect.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input required type="text" class="form-control" name="flight_level" id="flight_level" placeholder="e.g. 310" maxlength="3" value="{{ $flight_level ?? old('flight_level') }}">
                        <label for="flight_level">Requested {{ $isConcorde ? 'lower block' : 'oceanic' }} flight level (digits only, e.g. 340)</label>
                    </div>
                    @if (config('app.ctp_info_enabled'))
                        <div class="form-text"><b>Ensure you enter your assigned oceanic flight level as per your booking!</b></div>
                    @endif
                    @if ($flight_level)
                        <div class="form-text">Your requested flight level (the altitude on your flight plan) was automatically collected. You may change the flight level if it is incorrect.</div>
                    @endif
                </div>
                @if ($isConcorde)
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input required type="text" class="form-control" name="upper_flight_level" id="upper_flight_level" placeholder="e.g. 310" maxlength="3" value="{{ old('upper_flight_level') }}">
                            <label for="flight_level">Requested upper block flight level</label>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="max_flight_level" id="max_flight_level" placeholder="e.g. 390" maxlength="3" value="{{ old('max_flight_level') }}">
                            <label for="max_flight_level" class="uk-form-label">Maximum oceanic flight level</label>
                        </div>
                        @if (config('app.ctp_info_enabled'))
                            <div class="form-text"><b>Ensure you enter your max flight level as per your booking!</b></div>
                        @endif
                        <div class="form-text">This is the highest flight level you can accept.</div>
                    </div>
                @endif
                <div class="col-md-6">
                    <div class="form-floating">
                        <input required type="text" class="form-control" name="mach" id="mach" placeholder="e.g. 080" maxlength="3" value="{{ old('mach') }}">
                        <label for="mach" class="uk-form-label">Requested mach number (digits only, e.g. 080)</label>
                    </div>
                </div>
            </div>
            <h5 class="font-display">Route</h5>
            <div class="row gap-4 mb-4">
                <div class="col-auto">
                    <div class="form-floating">
                        <select class="form-select" id="track_id" name="track_id">
                            <option value="" selected>None</option>
                            @foreach($tracks as $track)
                                <option data-routeing="{{ $track->last_routeing }}" value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                            @endforeach
                        </select>
                        <label for="track_id">Requested NAT Track</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex justify-content-center">
                        <div class="fst-italic">or...</div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-floating">
                        <input value="{{ old('random_routeing') }}" type="text" class="form-control" name="random_routeing" id="random_routeing" placeholder="e.g. GOMUP 59/20 59/30 58/40 56/50 JANJO" onblur="this.value = this.value.toUpperCase()">
                        <label for="random_routeing">Requested random routeing</label>
                    </div>
                </div>
            </div>
            <h5 class="font-display">Oceanic entry</h5>
            <div class="row gap-4 mb-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <select class="form-select" id="target_datalink_authority_id" name="target_datalink_authority_id">
                            <option value="" selected>Select one...</option>
                            @foreach($datalinkAuthorities as $key => $value)
                                <option data-authority="{{ $key }}" value="{{ $key }}">{{ $key }} ({{ $value }})</option>
                            @endforeach
                        </select>
                        <label for="track_id">First oceanic sector</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input value="{{ old('entry_fix') }}" required type="text" class="form-control" name="entry_fix" id="entry_fix" placeholder="e.g. MALOT" maxlength="7" onblur="this.value = this.value.toUpperCase()">
                        <label for="entry_fix" class="uk-form-label">Entry fix</label>
                    </div>
                    <div class="form-text">The first fix/waypoint in oceanic airspace.</div>
                    <div class="uk-text-meta uk-text-bold" style="display: none;" id="oep-autofilled-msg">This fix was auto-filled, based on your selected track..</div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input value="{{ old('entry_time') }}" required type="number" class="form-control" name="entry_time" id="entry_time" placeholder="e.g. 1350">
                        <label for="entry_time" class="uk-form-label">Estimated time of arrival for entry fix</label>
                    </div>
                    <div class="form-text">You can find this in your FMC, providing your simulator is set to real time.</div>
                    <a class="form-text" target="_blank" href="https://knowledgebase.ganderoceanic.ca/nattrak/requesting-oceanic-clearance/#section-3-oceanic-entry">An example is available here.</a>
                </div>
            </div>
            <h5 class="font-display">Metadata</h5>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" value="{{ old('tmi') }}" required name="tmi" id="tmi" placeholder="e.g. 090" maxlength="4">
                        <label for="tmi" class="uk-form-label">Current TMI (available at top of page)</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" value="{{ old('free_text') }}" name="free_text" id="free_text">
                        <label for="free_text" class="uk-form-label">Free text</label>
                    </div>
                </div>
            </div>
            <div class="">
                <button type="submit" class="btn btn-success btn-lg">Submit Oceanic Clearance Request</button>
            </div>
            @if ($isConcorde)
                <input type="hidden" id="is_concorde" name="is_concorde" value="1">
            @else
                <input type="hidden" id="is_concorde" name="is_concorde" value="0">
            @endif
        </form>
    </div>
    <div class="modal fade" id="airportInfo" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Airports requiring clearance prior to departure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                        <th>Departure Point</th>
                        <th>Jet Departures</th>
                        <th>Non-Jet Departures</th>
                        </thead>
                        <tbody>
                        <tr>
                            <th>EIDW, EIWT, EIME</th>
                            <td>For all Oceanic entry points request when airborne.</td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>EICK</th>
                            <td>
                                If flight planned to enter Shanwick airspace via OMOKO, TAMEL or LASNO,
                                Oceanic clearance required prior to departure.
                                All other Oceanic entry points, if the elapsed time to Shanwick Entry Point
                                is 40 minutes or less, Oceanic clearance required prior to departure.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>EGAA, EGAC, EGAE, EGPF, EGPK</th>
                            <td>
                                If flight planned to enter Shanwick
                                at GOMUP, oceanic clearance is
                                required prior to departure.
                                For all other Oceanic entry points,
                                request when airborne.
                            </td>
                            <td>Request when airborne.</td>
                        </tr>
                        <tr>
                            <th>All other
                                aerodromes</th>
                            <td>
                                If the elapsed time to the Shanwick entry point is 40 minutes or less
                                oceanic clearance is required prior to departure.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        $("#track_id").change(function () {
            if ($("#is_concorde").val() == 1) return;

            if (this.value == '') {
                $("#entry_fix").prop('readonly', false).removeClass('form-control-plaintext').addClass('form-control').val('');
                $("#oep-autofilled-msg").hide();
                return;
            }
            const routeing = $(this).find(':selected').data("routeing");
            if (routeing == '' || routeing == null) {
                return;
            }
            $("#entry_fix").prop('readonly', true).addClass('form-control-plaintext').removeClass('form-control').val(routeing.replace(/ .*/,''));
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
