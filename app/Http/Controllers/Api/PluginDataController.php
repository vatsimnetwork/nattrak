<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use App\Models\Track;
use App\Services\VatsimDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PluginDataController extends Controller
{
    public VatsimDataService $dataService;

    public function __construct(VatsimDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    private function formatTime($time)
    {
        return substr($time, 0, 2).':'.substr($time, 2, 2);
    }

    private function formatClxData(Collection $data): Collection
    {
        return $data->map(function (ClxMessage $msg) {
            return [
                'callsign' => $msg->rclMessage->callsign,
                'status' => 'CLEARED',
                'nat' => $msg->track ? $msg->track->identifier : 'RR',
                'fix' => $msg->entry_fix,
                'level' => $msg->flight_level,
                'mach' => '0.'.substr($msg->mach, 1),
                'estimating_time' => $this->formatTime($msg->rclMessage->entry_time),
                'clearance_issued' => $msg->created_at,
                'extra_info' => $msg->free_text,
            ];
        });
    }

    private function formatRclData(Collection $data): Collection
    {
        return $data->map(function (RclMessage $msg) {
            return [
                'callsign' => $msg->callsign,
                'status' => $msg->clxMessages->count() > 0 ? 'CLEARED' : 'PENDING',
                'nat' => $msg->track ? $msg->track->identifier : 'RR',
                'fix' => $msg->entry_fix,
                'level' => $msg->flight_level,
                'mach' => '0.'.substr($msg->mach, 1),
                'estimating_time' => $this->formatTime($msg->entry_time),
                'clearance_issued' => $msg->clxMessages->count() > 0 ? $msg->clxMessages->first()->created_at : null,
                'extra_info' => $msg->free_text,
            ];
        });
    }

    public function allRclMessages(Request $request)
    {
        $rclMessages = RclMessage::when($request->has('track'), function (Builder $query) use ($request) {
            $track = Track::active()->whereIdentifier($request->get('track'))->first();
            if (! $track) {
                abort(400, "Track with identifier {$request->get('track')} not active at present time.");
            }
            $query->where('track_id', $track->id);
        })->get();

        return response($this->formatRclData($rclMessages));
    }

    public function detailedClxMessages(Request $request)
    {
        $trackToSortBy = null;
        $requestAsksForTrack = false;

        if ($request->has('track')) {
            $requestAsksForTrack = true;
            $trackToSortBy = Track::active()->whereIdentifier($request->get('track'))->firstOrFail();
        }

        $clxMessages = ClxMessage::where('overwritten', false)->when($requestAsksForTrack, function (Builder $query) use ($trackToSortBy) {
            $query->where('track_id', $trackToSortBy->id);
        })->orderByDesc('created_at')->get();

        $mapped = $clxMessages->map(function (ClxMessage $msg) {
            return [
                'id' => $msg->id,
                'time' => $msg->created_at,
                'controller' => [
                    'cid' => $msg->vatsimAccount->id ?? null,
                    'callsign' => $this->dataService->getActiveControllerData($msg->vatsimAccount)->callsign ?? null,
                    'datalink_authority' => $msg->datalink_authority->name,
                ],
                'pilot' => [
                    'cid' => $msg->rclMessage->vatsim_account_id ?? null,
                ],
                'callsign' => $msg->rclMessage->callsign,
                'dest' => $msg->rclMessage->destination,
                'route' => $msg->track ? $msg->track->last_routeing : $msg->random_routeing,
                'track' => $msg->track?->makeHidden(['created_at', 'updated_at', 'id']),
                'flight_level' => [
                    'requested' => $msg->rclMessage->flight_level,
                    'cleared' => $msg->flight_level,
                    'maximum' => $msg->rclMessage->max_flight_level,
                ],
                'mach' => [
                    'requested' => $msg->rclMessage->mach,
                    'cleared' => $msg->mach,
                ],
                'entry' => [
                    'fix' => $msg->entry_fix,
                    'estimate' => $this->formatTime($msg->rclMessage->entry_time),
                    'restriction' => $msg->entry_time_restriction,
                ],
                'extra_info' => $msg->free_text,
            ];
        });

        return response($mapped);
    }

    public function getTracks(Request $request)
    {
        $tracks = Track::active()->when($request->has('identifier'), function (Builder $query) use ($request) {
            $query->where('identifier', $request->get('identifier'))->firstOrFail();
        })->get()->makeHidden(['created_at', 'updated_at', 'id']);

        return response($tracks);
    }
}
