<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClxMessage;
use Illuminate\Support\Collection;

class PluginDataController extends Controller
{
    private function formatClxData(Collection $data): Collection
    {
        return $data->map(function (ClxMessage $msg) {
            return [
                'callsign' => $msg->rclMessage->callsign,
                'status' => 'CLEARED',
                'nat' => $msg->track ? $msg->track->identifier : 'RR',
                'fix' => $msg->entry_fix,
                'level' => $msg->flight_level,
                'mach' => '0.' . substr($msg->mach, 1),
                'estimating_time' => substr($msg->rclMessage->entry_time, 0, 2) . ':' . substr($msg->rclMessage->entry_time, 2, 2),
                'clearance_issued' => $msg->created_at,
                'extra_info' => $msg->free_text
            ];
        });
    }

    public function clxMessages()
    {
        return response($this->formatClxData(ClxMessage::all()));
    }
}
