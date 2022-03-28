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

    /**
     * Initialises VATSIM data service for grabbing active authority
     * @param VatsimDataService $dataService
     */
    public function __construct(VatsimDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function getPending(Request $request)
    {
        $track = null;
        if ($request->has('sortByTrack') && !in_array($request->get('sortByTrack'), ['all', 'rr'])) {
            $track = Track::active()->whereIdentifier($request->get('sortByTrack'))->firstOrFail();
        }

        return view('controllers.clx.pending', [
            'displayedTrack' => $track,
            'tracks' => Track::active()->get(),
            '_pageTitle' => $track ? "Track {$track->identifier}" : "All tracks"
        ]);
    }

    /**
     * Shows a pilot RCL message
     * GET /controllers/clx/rcl-msg/{rclMessage:id}
     */
    public function showRclMessage(RclMessage $rclMessage)
    {
        return view('controllers.clx.rcl-messages.show', [
            'message' => $rclMessage,
            'dlAuthorities' => DatalinkAuthorities::cases(),
            'activeDlAuthority' => $this->dataService->getActiveControllerAuthority(Auth::user()) ?? DatalinkAuthorities::NAT,
            '_pageTitle' => $rclMessage->callsign
        ]);
    }

    /**
     * Transmits the CLX message to the pilot
     * POST /controllers/clx/transmit/{rclMessage:id}
     */
    public function transmit(RclMessage $rclMessage, ClxMessageRequest $request)
    {
        /**
         * Detect whether the clearance is a reclearance
         */
        $isReclearance = $rclMessage->clxMessages->isNotEmpty();

        /**
         * Create the message
         */
        $clxMessage = new ClxMessage([
            'vatsim_account_id' => $request->user()->id,
            'rcl_message_id' => $rclMessage->id,
            'flight_level' => $request->filled('atc_fl') ? $request->get('atc_fl') : $rclMessage->flight_level,
            'mach' => $request->filled('atc_mach') ? $request->get('atc_mach') : $rclMessage->mach,
            'entry_fix' => $rclMessage->entry_fix,
            'track_id' => $rclMessage->track ? $rclMessage->track->id : null,
            'random_routeing' => $rclMessage->random_routeing ?? null,
            'entry_time_restriction' => $request->get('entry_time_requirement'),
            'free_text' => $isReclearance ? '** RECLEARANCE ' . now()->format('Hi') . ' ** ' : '' . $request->get('free_text'),
            'datalink_authority' => DatalinkAuthorities::from($request->get('datalink_authority'))
        ]);
        $clxMessage->save();

        /**
         * Assign Clx message to Rcl
         */
        $rclMessage->clx_message_id = $clxMessage->id;
        $rclMessage->save();

        activity('datalink')
            ->causedBy($clxMessage->vatsimAccount)
            ->performedOn($rclMessage)
            ->withProperties(['datalink' => $clxMessage->data_link_message])
            ->log("CLX Message Transmitted By " . $clxMessage->datalink_authority->name);

        toastr()->success('Clearance transmitted.');
        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }
}
