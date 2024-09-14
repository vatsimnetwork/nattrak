<div>
    @if (!$reject)
    <form wire:submit="submit">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="form-floating">
                    <input disabled maxlength="7" required type="text" class="form-control" name="callsign" id="callsign" placeholder="Enter callsign" value="{{ $rclMessage->callsign }}" onblur="this.value = this.value.toUpperCase()">
                    <label for="callsign" class="uk-form-label">Callsign</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input disabled required type="text" class="form-control" name="destination" id="destination" placeholder="Enter destination ICAO (e.g. EGLL)" maxlength="4" value="{{ $rclMessage->destination }}" onblur="this.value = this.value.toUpperCase()">
                    <label for="destination">Destination ICAO</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input disabled value="{{ $rclMessage->entry_fix }}" required type="text" class="form-control" name="entry_fix" id="entry_fix" placeholder="e.g. MALOT" maxlength="7" onblur="this.value = this.value.toUpperCase()">
                    <label for="entry_fix" class="uk-form-label">Entry fix</label>
                </div>
            </div>
        </div>
        <div class="row gap-4 mb-4">
            <div class="col-md-6">
                <div class="form-floating">
                    <input value="{{ old('entry_time') }}" required type="number" class="form-control" name="entry_time" wire:model.live="entryTime" id="entry_time" placeholder="e.g. 1350">
                    <label for="entry_time" class="uk-form-label">New estimated time of arrival for entry fix</label>
                </div>
                <div class="form-text">You can find this in your FMC, providing your simulator is set to real time.</div>
                <a class="form-text" target="_blank" href="https://knowledgebase.ganderoceanic.ca/nattrak/requesting-oceanic-clearance/#section-3-oceanic-entry">An example is available here.</a>
            </div>
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
        @if ($latestClxMessage)
            <p>
                Your existing clearance will be cancelled. Expect a re-issued clearance in your message history page shortly after submitting this form.
            </p>
        @endif
        <div class="">
            <button type="submit" class="btn btn-success btn-lg">Submit</button>
        </div>
    </form>
    @else
        <div class="alert alert-info">
            <p class="fw-bold">Use this page to notify the controller of a revised oceanic entry time after you have submitted oceanic clearance</p>
            <p><a href="{{ route('pilots.rcl.create') }}">Submit Oceanic Clearance</a></p>
        </div>
    @endif
</div>
