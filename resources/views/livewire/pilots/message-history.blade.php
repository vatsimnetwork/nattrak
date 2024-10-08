<div>
    <div class="mb-4">
            <h5>Controller Messages</h5>
            @if (count($clxMessages) == 0)
                <div class="fst-italic">No messages. @if(! config('app.rcl_auto_acknowledgement_enabled'))If you haven't received clearance within 10 minutes of requesting, contact the controller.@endif</div>
            @endif
            @foreach($clxMessages as $message)
                <div class="card" wire:key="clx-{{ $message['created_at'] }}">
                    <div class="card-body">
                        <div class="d-flex flex-row align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-arrow-down text-secondary" style="font-size: 2em;" ></i>
                            </div>
                            <div>
                                <h5>Oceanic Clearance Message - {{\Carbon\Carbon::parse($message['created_at'])}} Z</h5>
                                <p>
                                    from {{ $message['datalink_authority']['id'] }} ({{ $message['datalink_authority']['description'] }})
                                </p>
                            </div>
                        </div>
                        @if ($message['cancelled'])
                            <p class="fw-bold text-danger">
                                Clearance cancelled - {{ $message['cancellation_reason'] }}
                            </p>
                        @endif
                        <p style="font-family: monospace">
                            @foreach($message['datalink_message'] as $line)
                                {{ $line }}<br>
                            @endforeach
                        </p>
                        <p>
                            <b>{{ $message['simple_datalink_message'] }}</b>
                        </p>
                    </div>
                </div>
                @if (! $loop->last)
                    <hr>
                @endif
            @endforeach
    </div>
    <div class="mb-4">
        <h5>Automatic Messages</h5>
        @if (count($cpdlcMessages) == 0)
            <div class="fst-italic">No messages.</div>
        @endif
        @foreach($cpdlcMessages as $message)
            <div class="card" wire:key="cpdlc-{{ $message['created_at'] }}">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-arrow-down text-secondary" style="font-size: 2em;" ></i>
                        </div>
                        <h5>Message from {{ $message['datalink_authority']['id'] }} ({{ $message['datalink_authority']['description'] }}) - {{\Carbon\Carbon::parse($message['created_at'])}} Z</h5>
                    </div>
                    <p class="font-monospace">
                        {{ $message['message'] }}
                    </p>
                    <p>
                        <b>
                            {{ $message['caption'] ?? '' }}
                        </b>
                    </p>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mb-4">
        <h5>Oceanic Clearance Request Messages</h5>
        @if (count($rclMessages) == 0)
            <div class="fst-italic">No messages. Request clearance via the Request Clearance button on the pilots toolbar.</div>
        @endif
        @foreach($rclMessages as $message)
            <div class="card" wire:key="rcl-{{ $message['created_at'] }}">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-arrow-up text-secondary" style="font-size: 2em;" ></i>
                        </div>
                        <div>
                            <h5>Oceanic Clearance Request - {{ $message->request_time }}</h5>
                        </div>
                    </div>
                    <p style="font-family: monospace">
                        {{ $message->dataLinkMessage }}
                    </p>
                </div>
            </div>
            @if (! $loop->last)
                <hr>
            @endif
        @endforeach
        <p class="text-secondary mt-4">Last updated {{ now() }}</p>
    </div>
    <script>
        window.onload = function () {
            if (!("Notification") in window) {
                console.log('notifications not supported')
            }
            else if (Notification.permission != 'granted') {
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        new Notification("Thank you!", { body: 'You will be notified when your clearance is processed. '})
                    }
                })
            }
        }

        window.addEventListener('clx-received', event => {
            console.info('CLX received')
            if (Notification.permission === 'granted') {
                new Notification("Oceanic Clearance Message", { body: event.detail.dl })
            }
        })

        window.addEventListener('cpdlc-received', event => {
            console.info('cpdlc received')
            if (Notification.permission === 'granted') {
                new Notification("CPDLC Message", { body: event.detail.dl })
            }
        })
    </script>
</div>
