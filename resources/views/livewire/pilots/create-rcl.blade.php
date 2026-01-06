<div>
<div>
    <div class="d-flex flex-row justify-content-between mt-4 mb-2">
        <h1 class="fs-3 text-primary-emphasis font-display" id="start">Request oceanic clearance</h1>
        <button onclick="startTour()" class="btn btn-outline-primary">Tutorial</button>
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
    <p class="text-secondary">Enter all values as numbers only (e.g. 350 instead of FL350, 080 instead of Mach .80</p>
    <form wire:submit="submit">
    <div class="bg-body-secondary p-3 mt-5">
        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label for="" class="form-label form-text">Send request to
                </label>
                <select wire:model.blur="datalinkAuthorityId" class="form-select border-2 border-black rounded-0" id="target_datalink_authority_id" name="target_datalink_authority_id">
                    <option value="" selected>Select one...</option>
                    @foreach($datalinkAuthorities as $key => $value)
                        <option data-authority="{{ $key }}" value="{{ $key }}">{{ $key }} ({{ $value }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <p class="form-text"><br/>If NAT_FSS is online, select the oceanic sector you will enter first.</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col">
                <label for="" class="form-label form-text">Callsign
                    @if ($callsignPrefilled)
                        <a class="text-primary" href="#" data-bs-toggle="tooltip" data-bs-title="Your callsign was automatically collected from VATSIM. You can change the callsign if it is incorrect.">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-wifi" viewBox="0 0 16 16">
                                <path d="M15.384 6.115a.485.485 0 0 0-.047-.736A12.44 12.44 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.52.52 0 0 0 .668.05A11.45 11.45 0 0 1 8 4c2.507 0 4.827.802 6.716 2.164.205.148.49.13.668-.049"/>
                                <path d="M13.229 8.271a.482.482 0 0 0-.063-.745A9.46 9.46 0 0 0 8 6c-1.905 0-3.68.56-5.166 1.526a.48.48 0 0 0-.063.745.525.525 0 0 0 .652.065A8.46 8.46 0 0 1 8 7a8.46 8.46 0 0 1 4.576 1.336c.206.132.48.108.653-.065m-2.183 2.183c.226-.226.185-.605-.1-.75A6.5 6.5 0 0 0 8 9c-1.06 0-2.062.254-2.946.704-.285.145-.326.524-.1.75l.015.015c.16.16.407.19.611.09A5.5 5.5 0 0 1 8 10c.868 0 1.69.201 2.42.56.203.1.45.07.61-.091zM9.06 12.44c.196-.196.198-.52-.04-.66A2 2 0 0 0 8 11.5a2 2 0 0 0-1.02.28c-.238.14-.236.464-.04.66l.706.706a.5.5 0 0 0 .707 0l.707-.707z"/>
                            </svg>
                        </a>
                    @endif
                </label>
                <input onblur="this.value = this.value.toUpperCase()" wire:model.blur="callsign" type="text" class="form-control border-2 border-black rounded-0" placeholder="">
            </div>
            <div class="col">
                <label for="" class="form-label form-text">Destination (ICAO)
                    @if ($arrivalPrefilled)
                        <a class="text-primary" href="#" data-bs-toggle="tooltip" data-bs-title="Your destination was automatically collected from VATSIM. You can change the callsign if it is incorrect.">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-wifi" viewBox="0 0 16 16">
                                <path d="M15.384 6.115a.485.485 0 0 0-.047-.736A12.44 12.44 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.52.52 0 0 0 .668.05A11.45 11.45 0 0 1 8 4c2.507 0 4.827.802 6.716 2.164.205.148.49.13.668-.049"/>
                                <path d="M13.229 8.271a.482.482 0 0 0-.063-.745A9.46 9.46 0 0 0 8 6c-1.905 0-3.68.56-5.166 1.526a.48.48 0 0 0-.063.745.525.525 0 0 0 .652.065A8.46 8.46 0 0 1 8 7a8.46 8.46 0 0 1 4.576 1.336c.206.132.48.108.653-.065m-2.183 2.183c.226-.226.185-.605-.1-.75A6.5 6.5 0 0 0 8 9c-1.06 0-2.062.254-2.946.704-.285.145-.326.524-.1.75l.015.015c.16.16.407.19.611.09A5.5 5.5 0 0 1 8 10c.868 0 1.69.201 2.42.56.203.1.45.07.61-.091zM9.06 12.44c.196-.196.198-.52-.04-.66A2 2 0 0 0 8 11.5a2 2 0 0 0-1.02.28c-.238.14-.236.464-.04.66l.706.706a.5.5 0 0 0 .707 0l.707-.707z"/>
                            </svg>
                        </a>
                    @endif
                </label>
                <input type="text" class="form-control border-2 border-black rounded-0" placeholder="">
            </div>
        </div>
    </div>
    <div class="bg-body-secondary p-3 mt-3">
        <div class="row g-3 mb-2">
            @if (! $isConcorde)
                <div class="col">
                    <label for="" class="form-label form-text">Flight level</label>
                    <input type="text" maxlength="3" class="form-control border-2 border-black rounded-0">
                </div>
                <div class="col">
                    <label for="" class="form-label form-text">
                        <a href="#" class="text-reset text-decoration-none" style="border-bottom: 1px dotted;" data-bs-toggle="tooltip" data-bs-title="The maximum flight level you can accept at your oceanic entry. If unsure, check your 'max' flight level in the VNAV/PROG page of your FMS.">
                            Maximum flight level
                        </a>
                    </label>
                    <input type="text" maxlength="3" class="form-control border-2 border-black rounded-0">
                </div>
            @else
                <p class="mb-0">Concorde detected in VATSIM flight plan. Request clearance by voice if incorrect.</p>
                <div class="col">
                    <label for="" class="form-label form-text">Lower block level</label>
                    <input type="text" maxlength="3" class="form-control border-2 border-black rounded-0">
                </div>
                <div class="col">
                    <label for="" class="form-label form-text">Upper block level</label>
                    <input type="text" maxlength="3" class="form-control border-2 border-black rounded-0">
                </div>
            @endif
            <div class="col">
                <label for="" class="form-label form-text">Mach number</label>
                <input type="text" maxlength="3" class="form-control border-2 border-black rounded-0">
            </div>
        </div>
    </div>
    <div x-data="{ type: $wire.entangle('routingMode') }" class="bg-body-secondary p-3 mt-3">
        <div class="row g-3 mb-2">
            <div class="col-md-3">
                <label for="" class="form-label form-text">Routing type</label>
                <select x-model="type" class="form-select border-2 border-black rounded-0" id="target_datalink_authority_id" name="target_datalink_authority_id">
                    <option value="" disabled>Select one...</option>
                    <option value="track">Track</option>
                    <option value="rr">Random routing</option>
                </select>
            </div>
        </div>
        <div class="row g-3 mb-2">
            <div class="col-md-6" x-show="type == 'track'">
                <label for="" class="form-label form-text">Select requested track</label>
                <select class="form-select border-2 border-black rounded-0" id="track_id">
                    <option value="" selected>Select one...</option>
                    @foreach($tracks as $track)
                        <option data-routeing="{{ $track->last_routeing }}" value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col" x-show="type == 'rr'">
                <label for="" class="form-label form-text">Enter random routing</label>
                <input type="text" maxlength="3" class="form-control border-2 border-black rounded-0">
            </div>
        </div>
    </div>
    <div class="bg-body-secondary p-3 mt-3">
        <div class="row g-3 mb-2">
            <div class="col">
                <label for="" class="form-label form-text">Oceanic entry fix
                    <a id="oep-autofilled-msg" style="display: none;" class="text-primary" href="#" data-bs-toggle="tooltip" data-bs-title="Your entry fix was automatically filed based on your track.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-wifi" viewBox="0 0 16 16">
                            <path d="M15.384 6.115a.485.485 0 0 0-.047-.736A12.44 12.44 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.52.52 0 0 0 .668.05A11.45 11.45 0 0 1 8 4c2.507 0 4.827.802 6.716 2.164.205.148.49.13.668-.049"/>
                            <path d="M13.229 8.271a.482.482 0 0 0-.063-.745A9.46 9.46 0 0 0 8 6c-1.905 0-3.68.56-5.166 1.526a.48.48 0 0 0-.063.745.525.525 0 0 0 .652.065A8.46 8.46 0 0 1 8 7a8.46 8.46 0 0 1 4.576 1.336c.206.132.48.108.653-.065m-2.183 2.183c.226-.226.185-.605-.1-.75A6.5 6.5 0 0 0 8 9c-1.06 0-2.062.254-2.946.704-.285.145-.326.524-.1.75l.015.015c.16.16.407.19.611.09A5.5 5.5 0 0 1 8 10c.868 0 1.69.201 2.42.56.203.1.45.07.61-.091zM9.06 12.44c.196-.196.198-.52-.04-.66A2 2 0 0 0 8 11.5a2 2 0 0 0-1.02.28c-.238.14-.236.464-.04.66l.706.706a.5.5 0 0 0 .707 0l.707-.707z"/>
                        </svg>
                    </a>
                </label>
                <input type="text" id="entry_fix" class="form-control border-2 border-black rounded-0">
            </div>
            <div class="col">
                <label for="" class="form-label form-text">Estimated time of arrival at fix (Zulu)</label>
                <input type="text" class="form-control border-2 border-black rounded-0" maxlength="4">
            </div>
        </div>
    </div>
    <div class="bg-body-secondary p-3 mt-3">
        <div class="row g-3 mb-2">
            <div class="col-md-3">
                <label for="" class="form-label form-text">
                    <a href="#" class="text-reset text-decoration-none" style="border-bottom: 1px dotted;" data-bs-toggle="tooltip" data-bs-title="Track message identifier - found at top of page.">
                        TMI
                    </a>
                </label>
                <input type="text" class="form-control border-2 border-black rounded-0" maxlength="3">
            </div>
            <div class="col">
                <label for="" class="form-label form-text">Free text (optional)</label>
                <input type="text" class="form-control border-2 border-black rounded-0">
            </div>
        </div>
    </div>
    <div class="d-grid gap-2 col-6 mt-4">
        <button class="btn btn-primary" type="button">Submit Clearance Request</button>
    </div>
    <script type="module">
        window.onload = function () {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        };
    </script>
    </form>
</div>
<script type="module">
    $("#track_id").change(function () {
        // if ($("#is_concorde").val() == 1) return;

        if (this.value == '') {
            $("#entry_fix").prop('readonly', false).removeClass('form-control-plaintext').addClass('form-control').val('');
            $("#oep-autofilled-msg").hide();
            return;
        }
        const routeing = $(this).find(':selected').data("routeing");
        if (routeing == '' || routeing == null) {
            return;
        }
        $("#entry_fix").val(routeing.replace(/ .*/, ''));
        $("#oep-autofilled-msg").show();
    });
</script>

</div>
