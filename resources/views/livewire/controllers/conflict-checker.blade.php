<div wire:init="check" class="uk-text-small">
    <div wire:loading wire:target="check">
        <div uk-spinner="ratio: 1"></div>
        <span>Checking conflicts for FL{{$level}}, {{$time}}, {{$entry}}</span>
    </div>
    <div wire:loading.remove>
        <div class="uk-flex uk-flex-between">
            <span>Selected - conflicts for FL{{$level}}, {{$time}}, {{$entry}}</span>
            <button id="refreshButton" wire:click.prevent="check" class="uk-button uk-button-small">
                Refresh
            </button>
        </div>
    </div>
    <ul class="uk-list uk-list-striped uk-margin-remove">
        @foreach($conflicts as $conflict)
            <li>
                {{ $conflict['callsign'] }} - {{ $conflict['time'] }} - {{ $conflict['level'] }}
            </li>
        @endforeach
    </ul>
</div>

