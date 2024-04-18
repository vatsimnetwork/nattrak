<?php

namespace App\Http\Livewire\Controllers;

use App\Enums\ConflictLevelEnum;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use App\Models\Track;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

class ConflictChecker extends Component
{
    public $callsign;

    public $originalLevel;

    public $level;

    public $originalEntry;

    public $entry;

    public $originalTime;

    public $time;

    public $conflicts = [];

    public ConflictLevelEnum $conflictLevel = ConflictLevelEnum::None;

    public $pendingConflicts = [];

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
        if (strlen($newTime) != 4) return;
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
            $this->entry = strtok(Track::whereId($newTrackId)->firstOrFail()->last_routeing, ' ');
        }
        $this->check();
    }

    public function rrChanged(string $newRouteing)
    {
        if (empty($newRouteing)) {
            $this->entry = $this->originalEntry;
        } else {
            $this->entry = strtok($newRouteing, ' ');
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
            return 'Same';
        } else {
            return $a > $b ? "{$a->longAbsoluteDiffForHumans($b)} prior" : "{$a->longAbsoluteDiffForHumans($b)} after";
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

    private function fetchClearedConflicts(int $minutesSpan): Collection
    {
        $timeArray = $this->getTimeRange($this->time, $minutesSpan ?? 10);

        return ClxMessage::whereEntryFix($this->entry)
            ->whereNot(function (Builder $query) {
                $query->select('callsign')
                    ->from('rcl_messages')
                    ->whereColumn('rcl_messages.clx_message_id', 'clx_messages.id')
                    ->limit(1);
            }, $this->callsign)
            ->whereIn(
                'raw_entry_time_restriction',
                $timeArray
            )
            ->whereFlightLevel($this->level)
            ->with('rclMessage')
            ->get();
    }

    private function fetchPendingConflicts(int $minutesSpan): Collection
    {
        $timeArray = $this->getTimeRange($this->time, $minutesSpan ?? 10);

        return RclMessage::pending()
            ->where('callsign', '!=', $this->callsign)
            ->whereEntryFix($this->entry)
            ->whereIn('entry_time', $timeArray)
            ->whereFlightLevel($this->level)
            ->get();
    }

    private function mapClxMessages(Collection $messages): Collection
    {
        return $messages->map(function (ClxMessage $message, $key) {
            return [
                'id' => $key,
                'url' => route('controllers.clx.show-rcl-message', $message->rclMessage),
                'callsign' => $message->rclMessage->callsign,
                'level' => $message->flight_level,
                'mach' => $message->mach,
                'time' => $message->formatEntryTimeRestriction(),
                'diffVisual' => $this->formatDiff(Carbon::parse($this->time), Carbon::parse($message->raw_entry_time_restriction)),
                'diffMinutes' => Carbon::parse($this->time)->diffInMinutes(Carbon::parse($message->raw_entry_time_restriction)),
            ];
        });
    }

    private function mapRclMessages(Collection $messages): Collection
    {
        return $messages->map(function (RclMessage $message, $key) {
            return [
                'id' => $key,
                'url' => route('controllers.clx.show-rcl-message', $message),
                'callsign' => $message->callsign,
                'level' => $message->flight_level,
                'mach' => $message->mach,
                'time' => $message->entry_time,
                'diffVisual' => $this->formatDiff(Carbon::parse($this->time), Carbon::parse($message->entry_time)),
                'diffMinutes' => Carbon::parse($this->time)->diffInMinutes(Carbon::parse($message->entry_time)),
            ];
        });
    }

    public function check()
    {
        if (strlen($this->time) != 4) {
            return;
        }
        /**
         * Set cleared conflicts (CLX messages) to null, fetch them from DB, map them to array, determine conflict level, then set public variable.
         */
        $this->conflicts = [];
        $clearedConflicts = $this->fetchClearedConflicts(10);
        $clearedConflictsMapped = $this->mapClxMessages($clearedConflicts);
        $this->conflictLevel = $this->determineConflictLevel($clearedConflictsMapped);
        $this->conflicts = $clearedConflictsMapped;

        // Same but for pending RCLs
        $this->pendingConflicts = [];
        $pendingConflicts = $this->fetchPendingConflicts(10);
        $this->pendingConflicts = $this->mapRclMessages($pendingConflicts);
    }
}
