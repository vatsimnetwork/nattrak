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
    <div style="margin-bottom: 5px;">
        <div wire:loading wire:target="check">
            <div uk-spinner="ratio: 1"></div>
            <span class="uk-text-meta">Checking conflicts for FL{{$level}}, {{$time}}, {{$entry}}</span>
        </div>
        <div wire:loading.remove>
            <div class="uk-flex uk-flex-between uk-flex-middle">
                <span class="uk-text-meta">FL{{$level}}, {{$time}}, {{$entry}}</span>
                <a id="refreshButton" wire:click.prevent="check" class="uk-button uk-button-default uk-button-small">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>
    </div>
    @switch ($conflictLevel)
        @case(\App\Enums\ConflictLevelEnum::None)
            <div class="uk-alert uk-alert-success uk-margin-remove" uk-alert>
                <p>No Active Conflicts Detected Within 10 Minutes</p>
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
    <h5 class="uk-heading-line uk-margin-remove" style="margin: 10px 0px 10px 0px !important;"><span>Conflicts with Active traffic</span></h5>
    <ul class="uk-list uk-table-hover uk-margin-remove">
        @foreach($conflicts as $conflict)
            <li>
                <div class="uk-flex uk-flex-row uk-flex-middle">
                    @if ($conflict['diffMinutes'] < 5)
                        <span class="uk-badge conflict-warning" style="margin-right: 1em;"></span>
                    @elseif ($conflict['diffMinutes'] < 11)
                        <span class="uk-badge conflict-potential" style="margin-right: 1em;"></span>
                    @endif
                    <span>
                        <a href="{{ $conflict['url'] }}" class="uk-link-text uk-text-bold" style="border-bottom: 1px grey dotted;">{{ $conflict['callsign'] }}</a> - {{ $conflict['time'] }} ({{ $conflict['diffVisual']  }}) - FL{{ $conflict['level'] }} M{{ $conflict['mach'] }}
                    </span>
                </div>
            </li>
        @endforeach
        @if (count($conflicts) == 0)
            <li>
                None found
            </li>
        @endif
    </ul>
    <h5 class="uk-heading-line uk-margin-remove" style="margin: 10px 0px 10px 0px !important;"><span>Conflicts with Pending traffic</span></h5>
    <ul class="uk-list uk-table-hover uk-margin-remove">
        @foreach($pendingConflicts as $conflict)
            <li>
                <div class="uk-flex uk-flex-row uk-flex-middle">
                    @if ($conflict['diffMinutes'] < 5)
                        <span class="uk-badge conflict-warning" style="margin-right: 1em;"></span>
                    @elseif ($conflict['diffMinutes'] < 11)
                        <span class="uk-badge conflict-potential" style="margin-right: 1em;"></span>
                    @endif
                    <span>
                        <a href="{{ $conflict['url'] }}" class="uk-link-text uk-text-bold" style="border-bottom: 1px grey dotted;">{{ $conflict['callsign'] }}</a> - {{ $conflict['time'] }} ({{ $conflict['diffVisual']  }}) - FL{{ $conflict['level'] }} M{{ $conflict['mach'] }}
                    </span>
                </div>
            </li>
        @endforeach
        @if (count($pendingConflicts) == 0)
            <li>
                None found
            </li>
        @endif
    </ul>
</div>

