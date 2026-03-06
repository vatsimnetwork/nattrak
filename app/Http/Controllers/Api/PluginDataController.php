<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use App\Models\Track;
use App\Services\VatsimDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PluginDataController extends Controller
{
    public function __construct(
        public VatsimDataService $dataService
    ) {
    }

    public function allRclMessages(Request $request)
    {
        $track = null;
        if ($trackIdent = $request->get('track')) {
            $track = Track::active()->whereIdentifier($trackIdent)->first();
            if (! $track) {
                abort(400, "Track with identifier $trackIdent not active at present time.");
            }
        }

        return Cache::remember(
            'rcl-messages:'.($track->id ?? 'all'),
            60,
            fn () => RclMessage::with(['clxMessages', 'track', 'latestClxMessage'])
                ->when($track, fn (Builder $q) => $q->whereBelongsTo($track))
                ->get()
                ->map(self::serializeRclMessage(...))
        );
    }

    public function allClxMessages(Request $request)
    {
        $track = null;
        if ($trackIdent = $request->get('track')) {
            $track = Track::active()->whereIdentifier($trackIdent)->first();
            if (! $track) {
                abort(400, "Track with identifier $trackIdent not active at present time.");
            }
        }

        return Cache::remember(
            'clx-messages:'.($track->id ?? 'all'),
            60,
            fn () => ClxMessage::with(['rclMessage', 'track'])
                ->when($track, fn (Builder $q) => $q->whereBelongsTo($track))
                ->get()
                ->map(self::serializeClxMessage(...))
        );
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
        })->orderByDesc('created_at')->with('vatsimAccount', 'rclMessage', 'track')->get();

        $mapped = $clxMessages->map(function (ClxMessage $msg) {
            return [
                'id' => $msg->id,
                'time' => $msg->created_at,
                'controller' => [
                    'cid' => $msg->vatsimAccount->id ?? null,
                    'callsign' => $msg->datalinkAuthority->id,
                    'datalink_authority' => $msg->datalinkAuthority->id,
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
                    'cto' => $msg->cto_time,
                    'estimate' => $msg->rclMessage->entry_time,
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

    private static function isOnTrackorRR(RclMessage $msg)
    {
        if ($msg->latestClxMessage) {
            if ($msg->latestClxMessage->track) {
                return $msg->latestClxMessage->track->identifier;
            }

            // Fallback to the original RCL track if the CLX has no track
            if ($msg->track) {
                return $msg->track->identifier;
            }

            return 'RR';
        }

        // No CLX yet: use the RCL track if present, otherwise RR
        if ($msg->track) {
            return $msg->track->identifier;
        }

        return 'RR';
    }

    private static function serializeRclMessage(RclMessage $msg): array
    {
        return [
            'callsign' => $msg->callsign,
            'status' => $msg->clxMessages->count() ? 'CLEARED' : 'PENDING',
            'nat' => self::isOnTrackOrRr($msg),
            'fix' => $msg->latestClxMessage ? $msg->latestClxMessage->entry_fix : $msg->entry_fix,
            'level' => $msg->latestClxMessage ? $msg->latestClxMessage->flight_level : $msg->flight_level,
            'mach' => '0.'.substr($msg->mach, 1),
            'estimating_time' => $msg->latestClxMessage ? self::formatTime($msg->latestClxMessage->cto_time) : self::formatTime($msg->entry_time),
            'clearance_issued' => $msg->latestClxMessage?->created_at,
            'extra_info' => $msg->free_text,
        ];
    }

    private static function serializeClxMessage(ClxMessage $msg): array
    {
        return [
            'callsign' => $msg->callsign,
            'status' => 'CLEARED',
            'nat' => $msg->track->identifier ?? 'RR',
            'fix' => $msg->entry_fix,
            'level' => $msg->flight_level,
            'mach' => '0.'.substr($msg->mach, 1),
            'estimating_time' => self::formatTime($msg->rclMessage?->entry_time ?? '00:00'),
            'clearance_issued' => $msg->created_at,
            'extra_info' => $msg->rclMessage?->free_text,
        ];
    }

    private static function formatTime($time)
    {
        return substr($time, 0, 2).':'.substr($time, 2, 2);
    }
}
