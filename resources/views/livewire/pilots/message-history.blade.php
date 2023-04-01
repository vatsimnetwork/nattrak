<div>
    <div wire:poll.15s.keep-alive="pollForMessages">
        <h3>CPDLC Messages</h3>
        @foreach($cpdlcMessages as $message)
            <div class="uk-card uk-card-small uk-card-body">
                <div class="uk-flex uk-flex-row">
                    <div class="uk-margin-right">
                        <i class="fas fa-arrow-down uk-text-secondary" style="font-size: 2em;" ></i>
                    </div>
                    <div>
                        <h5>Message from {{ \App\Models\CpdlcMessage::whereId($message['id'])->first()->datalink_authority->name }}</h5>
                        <p>
                            // {{ \App\Models\CpdlcMessage::whereId($message['id'])->first()->free_text }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
        <h3>Clearance Messages</h3>
        @foreach($clxMessages as $message)
            <div class="uk-card uk-card-small uk-card-body">
                <div class="uk-flex uk-flex-row">
                    <div class="uk-margin-right">
                        <i class="fas fa-arrow-down uk-text-secondary" style="font-size: 2em;" ></i>
                    </div>
                    <div>
                        <h5>Oceanic Clearance Message - {{\Carbon\Carbon::parse($message['created_at'])}}</h5>
                    </div>
                </div>
                <div>
                    @if(\App\Models\ClxMessage::whereId($message['id'])->exists())
                        <p style="font-family: monospace">
                            @foreach(\App\Models\ClxMessage::whereId($message['id'])->first()->datalink_message as $line)
                                {{ $line }}<br>
                            @endforeach
                        </p>
                        <p>
                            <b>{{ \App\Models\ClxMessage::whereId($message['id'])->first()->simple_datalink_message }}</b>
                        </p>
                    @else
                        <p class="uk-text-italic">Clearance withdrawn.</p>
                    @endif
                </div>
            </div>
            @if (! $loop->last)
                <hr>
            @endif
        @endforeach
        <h3>Request Messages</h3>
        @foreach($rclMessages as $message)
            <div class="uk-card uk-card-small uk-card-body">
                <div class="uk-flex uk-flex-row">
                    <div class="uk-margin-right">
                        <i class="fas fa-arrow-up uk-text-secondary" style="font-size: 2em;" ></i>
                    </div>
                    <div>
                        <h5>Oceanic Clearance Request - {{ $message->request_time }}</h5>
                    </div>
                </div>
                <p style="font-family: monospace">
                    {{ $message->dataLinkMessage }}
                </p>
            </div>
            @if (! $loop->last)
                <hr>
            @endif
        @endforeach
        <p class="uk-text-meta">Last updated {{ now() }}</p>
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
