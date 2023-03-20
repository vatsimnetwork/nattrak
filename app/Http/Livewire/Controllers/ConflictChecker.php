<?php

namespace App\Http\Livewire\Controllers;

use App\Models\ClxMessage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
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

    protected $listeners = ['levelChanged', 'timeChanged'];

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

    public function check()
    {
        $timeArray = $this->getTimeRange($this->time, 5);
        Log::info($timeArray);

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
                'time' => $message->formatEntryTimeRestriction()
            ];
        });
        $this->conflicts = $mapped;
    }
}
