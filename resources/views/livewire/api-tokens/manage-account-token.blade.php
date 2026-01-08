<div>
    <div class="container">
        <h1 class="fs-2 font-display text-primary-emphasis">Manage API token</h1>
        <p>An API token will allow you to interact with natTrak using plugins in your controller client. Please refer to your oceanic controll area for further information.</p>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi flex-shrink-0 me-2" width="24" height="24" role="img" viewBox="0 0 16 16">
                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
            </svg>
            <div>
                Your token will not work if you are not connected to the network as an oceanic controller. To override this behaviour, your OCA management will need to add you to natTrak as an online-check exempt user.
                <br>
                <br>
                Tokens will expire after 365 days and will need to be re-issued.
            </div>
        </div>
        <div class="mt-5 p-4 bg-secondary-subtle">
            <h4 class="fs-4 font-display">Your API token</h4>
            @if (count(auth()->user()->tokens) >= 1)
                @foreach(auth()->user()->tokens as $token)
                    Created at {{ $token->created_at->toDateTimeString() }}, expires {{ $token->expires_at->toDateTimeString() }}
                @endforeach
            @else
                <p>No tokens found.</p>
                <button type="button" class="btn btn-primary" wire:click="generateToken">Generate token</button>
            @endif
        </div>
    </div>
</div>
