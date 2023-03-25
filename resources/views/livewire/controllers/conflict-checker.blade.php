<div wire:init="check" class="uk-text-small">
    <style>
        .conflict-green {
            background-color: #70a73b !important;
            color: #000 !important;
        }

        .conflict-potential {
            background-color: #FEC700 !important;
            color: #000 !important;
        }

        .conflict-warning {
            background-color: #db1f00 !important;
            color: #fff !important;
        }
    </style>
    <div wire:loading wire:target="check">
        <div uk-spinner="ratio: 1"></div>
        <span>Checking conflicts for FL{{$level}}, {{$time}}, {{$entry}}</span>
    </div>
    <div wire:loading.remove>
        <div class="uk-flex uk-flex-between">
            <span>Selected - conflicts for FL{{$level}}, {{$time}}, {{$entry}}</span>
            <a id="refreshButton" wire:click.prevent="check" class="uk-button uk-button-small">
                Refresh
            </a>
        </div>
    </div>
    @switch ($conflictLevel)
        @case(\App\Enums\ConflictLevelEnum::None)
            <div class="uk-alert conflict-green uk-margin-remove" uk-alert>
                <p>No Conflict Detected Within 10 Minutes</p>
            </div>
        @break
        @case(\App\Enums\ConflictLevelEnum::Potential)
            <div class="uk-alert conflict-potential uk-margin-remove" uk-alert>
                <p>Potential Conflict - Aircraft 5-10 minutes of aircraft on current selection</p>
            </div>
        @break
        @case(\App\Enums\ConflictLevelEnum::Warning)
            <div class="uk-alert conflict-warning uk-margin-remove" uk-alert>
                <p>Warning - Traffic within 5 minutes of aircraft on current selection</p>
            </div>
        @endswitch
    <ul class="uk-list uk-list-striped uk-margin-remove">
        @foreach($conflicts as $conflict)
            <li>
                {{ $conflict['callsign'] }} - {{ $conflict['time'] }} ({{ $conflict['diffVisual']  }}) - {{ $conflict['level'] }}
            </li>
        @endforeach
    </ul>
</div>

