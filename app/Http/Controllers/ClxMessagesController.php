<?php

namespace App\Http\Controllers;

use App\Enums\DatalinkAuthorities;
use App\Http\Requests\ClxMessageRequest;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use App\Models\Track;
use App\Services\VatsimDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClxMessagesController extends Controller
{
    public VatsimDataService $dataService;

    public function __construct(VatsimDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function getPending(Request $request)
    {
        $track = null;
        if ($request->has('sortByTrack') && !in_array($request->get('sortByTrack'), ['all', 'rr'])) {
            $track = Track::where('active', true)->where('identifier', $request->get('sortByTrack'))->firstOrFail();
        }

        return view('controllers.clx.pending', [
            'displayedTrack' => $track,
            'tracks' => Track::where('active', true)->get(),
            '_pageTitle' => $track ? "Track {$track->identifier}" : "All tracks"
        ]);
    }

    public function showRclMessage(RclMessage $rclMessage)
    {
        //dd($this->dataService->getActiveControllerAuthority(Auth::user()));
        return view('controllers.clx.rcl-messages.show', [
            'message' => $rclMessage,
            'dlAuthorities' => DatalinkAuthorities::cases(),
            'activeDlAuthority' => $this->dataService->getActiveControllerAuthority(Auth::user()) ?? DatalinkAuthorities::NAT,
            '_pageTitle' => $rclMessage->callsign
        ]);
    }

    public function transmit(RclMessage $rclMessage, ClxMessageRequest $request)
    {
        $reclearance = $rclMessage->clxMessages->count() > 0;
        $clxMessage = new ClxMessage();
        $clxMessage->vatsim_account_id = Auth::id();
        $clxMessage->rcl_message_id = $rclMessage->id;
        $clxMessage->flight_level = $request->filled('atc_fl') ? $request->get('atc_fl') : $rclMessage->flight_level;
        $clxMessage->mach = $request->filled('atc_mach') ? $request->get('atc_mach') : $rclMessage->mach;
        $clxMessage->entry_fix = $rclMessage->entry_fix;
        $clxMessage->track_id = $rclMessage->track ? $rclMessage->track_id : null;
        $clxMessage->random_routeing = $rclMessage->random_routeing ? $rclMessage->random_routeing : null;
        $clxMessage->entry_time_restriction = $request->get('entry_time_requirement');
        $clxMessage->free_text = $reclearance ? '** RECLEARANCE ' . now()->format('Hi') . ' ** ' : '' . $request->get('free_text');
        $clxMessage->datalink_authority = $request->get('datalink_authority');
        $clxMessage->save();
        $rclMessage->clx_message_id = $clxMessage->id;
        $rclMessage->save();

        toastr()->success('Clearance transmitted.');
        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }
}
