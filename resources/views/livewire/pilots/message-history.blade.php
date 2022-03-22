<div wire:poll>
    @foreach($rclMessages as $message)
        <div class="p-3 border d-flex flex-row ">
            <div class="mr-2">
                <i class="fas fa-arrow-up" style="font-size: 2em;" ></i>
            </div>
            <div>
                <p class="lead">Oceanic Clearance Request - {{ $message->request_time }}</p>
            </div>
        </div>
    @endforeach
    <script>
        document.addEventListener('livewire:load', function () {
            alert('test')
        });
        window.addEventListener('check-if-notify', event => {
            console.log('test');
           /* if (!("Notification") in window) {
                console.log('notifications not supported')
            }
            else if (Notification.permission != 'granted') {*/
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        new Notification("Thank you!")
                    } else {
                        console.log('Permission denied')
                    }
                })
            //}
        })
    </script>
</div>
