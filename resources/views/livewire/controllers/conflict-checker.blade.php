<div wire:init="check" class="text-small">
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
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="text-secondary">Checking conflicts for FL{{$level}}, {{$time}}, {{$entry}}</span>
        </div>
        <div wire:loading.remove>
            <div class="d-flex flex-row justify-content-between align-items-center">
                <span class="text-secondary">FL{{$level}}, {{$time}}, {{$entry}}</span>
                <a id="refreshButton" wire:click.prevent="check" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>
    </div>
    @switch ($conflictLevel)
        @case(\App\Enums\ConflictLevelEnum::None)
            <div class="alert alert-success" uk-alert>
                <span>No Active Conflicts Detected Within 10 Minutes</span>
            </div>
        @break
        @case(\App\Enums\ConflictLevelEnum::Potential)
            <div class="alert conflict-potential" uk-alert>
                <span>Potential Conflict - Aircraft 5-10 minutes of aircraft on current selection</span>
            </div>
        @break
        @case(\App\Enums\ConflictLevelEnum::Warning)
            <div class="alert conflict-warning" uk-alert>
                <span>Warning - Traffic within 5 minutes of aircraft on current selection</span>
            </div>
    @endswitch
    <h5 class="mb-3">Conflicts with Active Traffic</h5>
    <ul class="list-group">
        @foreach($conflicts as $conflict)
            <li class="list-group-item">
                <div class="d-flex flex-row align-items-center">
                    @if ($conflict['diffMinutes'] < 5)
                        <span class="p-2 rounded-circle conflict-warning" style="margin-right: 1em;">
                            <span class="visually-hidden">Warning</span>
                        </span>
                    @elseif ($conflict['diffMinutes'] < 11)
                        <span class="p-2 rounded-circle conflict-potential" style="margin-right: 1em;">
                            <span class="visually-hidden">Potential</span>
                        </span>
                    @endif
                    <span>
                        <a href="{{ $conflict['url'] }}" style="border-bottom: 1px grey dotted;">{{ $conflict['callsign'] }}</a> - {{ $conflict['time'] }} ({{ $conflict['diffVisual']  }}) - FL{{ $conflict['level'] }} M{{ $conflict['mach'] }}
                    </span>
                </div>
            </li>
        @endforeach
        @if (count($conflicts) == 0)
            <li class="list-group-item">
                None found
            </li>
        @endif
    </ul>
    <h5 class="my-3">Conflicts with Pending Traffic</h5>
    <ul class="list-group">
        @foreach($pendingConflicts as $conflict)
            <li class="list-group-item">
                <div class="d-flex flex-row align-items-center">
                    @if ($conflict['diffMinutes'] < 5)
                        <span class="p-2 rounded-circle conflict-warning" style="margin-right: 1em;">
                            <span class="visually-hidden">Warning</span>
                        </span>
                    @elseif ($conflict['diffMinutes'] < 11)
                        <span class="p-2 rounded-circle conflict-potential" style="margin-right: 1em;">
                            <span class="visually-hidden">Potential</span>
                        </span>
                    @endif
                    <span>
                        <a href="{{ $conflict['url'] }}" class="uk-link-text uk-text-bold" style="border-bottom: 1px grey dotted;">{{ $conflict['callsign'] }}</a> - {{ $conflict['time'] }} ({{ $conflict['diffVisual']  }}) - FL{{ $conflict['level'] }} M{{ $conflict['mach'] }}
                    </span>
                </div>
            </li>
        @endforeach
        @if (count($pendingConflicts) == 0)
            <li class="list-group-item">
                None found
            </li>
        @endif
    </ul>
</div>

