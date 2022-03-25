<div>
    <div wire:poll.keep-alive="pollForClx">
        @foreach($clxMessages as $message)
            <div class="p-3 border d-flex flex-row ">
                <div class="mr-2">
                    <i class="fas fa-arrow-down" style="font-size: 2em;" ></i>
                </div>
                <div>
                    <p class="lead">Oceanic Clearance Message - {{\Carbon\Carbon::parse($message['created_at'])}}</p>
                    <p>
                        @foreach(\App\Models\ClxMessage::whereId($message['id'])->first()->dataLinkMessage as $line)
                            {{ $line }}<br>
                        @endforeach
                    </p>
                    <p>
                       <b>{{ \App\Models\ClxMessage::whereId($message['id'])->first()->simpleMessage }}</b>
                    </p>
                </div>
            </div>
        @endforeach
        @foreach($rclMessages as $message)
            <div class="p-3 border d-flex flex-row ">
                <div class="mr-2">
                    <i class="fas fa-arrow-up" style="font-size: 2em;" ></i>
                </div>
                <div>
                    <p class="lead">Oceanic Clearance Request - {{ $message->request_time }}</p>
                    <p>
                        {{ $message->dataLinkMessage }}
                    </p>
                </div>
            </div>
        @endforeach
        <p class="small text-muted">Last updated {{ now() }}</p>
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
    </script>
</div>
