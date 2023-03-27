<?php

namespace App\Http\Livewire\Controllers;

use App\Enums\ConflictLevelEnum;
use App\Models\ClxMessage;
use App\Models\Track;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Livewire\Component;

class ConflictChecker extends Component
{
    public $originalLevel;
    public $level;
    public $originalEntry;
    public $entry;
    public $originalTime;
    public $time;

    public $conflicts = [];
    public ConflictLevelEnum $conflictLevel = ConflictLevelEnum::None;

    protected $listeners = ['levelChanged', 'timeChanged', 'trackChanged', 'rrChanged'];

    public function render()
    {
        return view('livewire.controllers.conflict-checker');
    }

    public function mount()
    {
        $this->originalLevel = $this->level;
        $this->originalEntry = $this->entry;
        $this->originalTime = $this->time;
    }

    public function levelChanged(string $newLevel)
    {
        if (empty($newLevel)) {
            $this->level = $this->originalLevel;
        } else {
            $this->level = $newLevel;
        }
        $this->check();
    }

    public function timeChanged(string $newTime)
    {
        if (empty($newTime)) {
            $this->time = $this->originalTime;
        } else {
            $this->time = $newTime;
        }
        $this->check();
    }

    public function trackChanged(string $newTrackId)
    {
        if (empty($newTrackId)) {
            $this->entry = $this->originalEntry;
        } else {
            $this->entry = strtok(Track::whereId($newTrackId)->firstOrFail()->last_routeing, " ");
        }
    }

    public function rrChanged(string $newRouteing)
    {
        if (empty($newRouteing)) {
            $this->entry = $this->originalEntry;
        } else {
            $this->entry = strtok($newRouteing, " ");
        }
        $this->check();
    }

    private function getTimeRange(string $time, int $minutes): array
    {
        $period = CarbonPeriod::since(Carbon::parse($time)->subMinutes($minutes))->minutes()->until(Carbon::parse($time)->addMinutes($minutes));
        $times = [];
        foreach ($period as $time) {
            $times[] = $time->format('Hi');
        }
        return $times;
    }

    private function formatDiff(Carbon $a, Carbon $b): string
    {
        if ($a->diffInMinutes($b) < 2) {
            return "Same";
        } else {
            return $a->longAbsoluteDiffForHumans($b);
        }
    }

    private function determineConflictLevel(Collection $aircraft): ConflictLevelEnum
    {
        $level = ConflictLevelEnum::None;
        foreach ($aircraft as $a) {
            if ($a['diffMinutes'] < 5) {
                $level = ConflictLevelEnum::Warning;
            }
            if ($a['diffMinutes'] >= 5 && $a['diffMinutes'] <= 10) {
                $level = ConflictLevelEnum::Potential;
            }
        }

        return $level;
    }

    public function check()
    {
        $timeArray = $this->getTimeRange($this->time, 10);
        $this->conflicts = [];
        $results = ClxMessage::whereEntryFix($this->entry)
            ->whereIn(
                'raw_entry_time_restriction',
                $timeArray
            )
            ->whereFlightLevel($this->level)
            ->with('rclMessage')
            ->get();
        $mapped = $results->map(function (ClxMessage $message, $key) {
            return [
                'id' => $key,
                'callsign' => $message->rclMessage->callsign,
                'level' => $message->flight_level,
                'time' => $message->formatEntryTimeRestriction(),
                'diffVisual' => $this->formatDiff(Carbon::parse($this->time), Carbon::parse($message->raw_entry_time_restriction)),
                'diffMinutes' => Carbon::parse($this->time)->diffInMinutes(Carbon::parse($message->raw_entry_time_restriction))
            ];
        });
        $this->conflictLevel = $this->determineConflictLevel($mapped);
        $this->conflicts = $mapped;
    }
}
