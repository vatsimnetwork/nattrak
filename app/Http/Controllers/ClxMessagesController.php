<?php

namespace App\Http\Controllers;

use App\Enums\DatalinkAuthorities;
use App\Http\Requests\ClxMessageRequest;
use App\Models\ClxMessage;
use App\Models\CpdlcMessage;
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

    public function getProcessed(Request $request)
    {
        $track = null;
        if ($request->has('sortByTrack') && !in_array($request->get('sortByTrack'), ['all', 'rr'])) {
            $track = Track::active()->whereIdentifier($request->get('sortByTrack'))->firstOrFail();
        }

        $processedRclMsgs = RclMessage::cleared()->when($track, function ($query) use ($track) {
            $query->whereTrackId($track->id);
        })->orderBy('request_time')->get();

        return view('controllers.clx.processed', [
            'displayedTrack' => $track,
            'tracks' => Track::active()->get(),
            'processedRclMsgs' => $processedRclMsgs,
            '_pageTitle' => $track ? "Track {$track->identifier}" : "All tracks"
        ]);
    }

    /**
     * Shows a pilot RCL message
     * GET /controllers/clx/rcl-msg/{rclMessage:id}
     */
    public function showRclMessage(RclMessage $rclMessage)
    {
        if (! $rclMessage->isEditLocked()) {
            $rclMessage->edit_lock = true;
            $rclMessage->edit_lock_time = now();
            $rclMessage->edit_lock_vatsim_account_id = Auth::id();
            $rclMessage->save();
        }

        return view('controllers.clx.rcl-messages.show', [
            'message' => $rclMessage,
            'dlAuthorities' => DatalinkAuthorities::cases(),
            'tracks' => Track::active()->get(),
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
         * If new track or RR requested..
         */
        $newTrack = null;
        $newEntryFix = null;
        if ($request->filled('new_track_id')) {
            $newTrack = Track::active()->where('id', $request->get('new_track_id'))->first();
            if (! $newTrack) {
                toastr()->error('Track not found');
                return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
            }
            $newEntryFix = strtok($newTrack->last_routeing, " ");
        }
        elseif ($request->filled('new_random_routeing')) {
            $newEntryFix = strtok($request->get('new_random_routeing'), " ");
        }

        /**
         * Formulate entry time requirement if needed..
         */
        $entryRequirement = null;
        if ($request->filled('entry_time_type') && $request->filled('entry_time_requirement')) {
            $entryRequirement = "{$request->get('entry_time_type')}{$request->get('entry_time_requirement')}";
        }

        /**
         * Create the message
         */
        $clxMessage = new ClxMessage([
            'vatsim_account_id' => $request->user()->id,
            'rcl_message_id' => $rclMessage->id,
            'flight_level' => $request->filled('atc_fl') ? $request->get('atc_fl') : $rclMessage->flight_level,
            'mach' => $request->filled('atc_mach') ? $request->get('atc_mach') : $rclMessage->mach,
            'entry_fix' => $newEntryFix ?? $rclMessage->entry_fix,
            'entry_time_restriction' => $entryRequirement ?? null,
            'free_text' => $isReclearance ? '** RECLEARANCE ' . now()->format('Hi') . ' ** ' : '' . $request->get('free_text'),
            'datalink_authority' => DatalinkAuthorities::from($request->get('datalink_authority'))
        ]);

        /**
         * Assign track or RR
         */
        if ($rclMessage->track || $newTrack) {
            $clxMessage->track_id = $newTrack ? $newTrack->id : $rclMessage->track->id;
            $clxMessage->random_routeing = null;
        }
        elseif ($rclMessage->random_routeing || $request->filled('new_random_routeing')) {
            $clxMessage->random_routeing = $request->filled('new_random_routeing') ? $request->get('new_random_routeing') : $rclMessage->random_routeing;
            $clxMessage->track_id = null;
        }

        /**
         * Save
         */
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

    public function deleteRclMessage(Request $request, RclMessage $rclMessage)
    {
        $redirectToProcessed = $rclMessage->clxMessages->count() > 0;
        $rclMessage->delete();
        toastr()->info('RCL deleted.');
        if ($redirectToProcessed) {
            return redirect()->route('controllers.clx.processed');
        } else {
            return redirect()->route('controllers.clx.pending');
        }
    }

    public function revertToVoice(Request $request, RclMessage $rclMessage)
    {
        CpdlcMessage::create([
            'pilot_id' => $rclMessage->vatsim_account_id,
            'pilot_callsign' => $rclMessage->callsign,
            'datalink_authority' => $this->dataService->getActiveControllerAuthority(Auth::user()) ?? DatalinkAuthorities::NAT,
            'free_text' => 'REVERT TO VOICE. REQUEST FREQ FROM DOMESTIC CONTROL.'
        ]);

        toastr()->success('Revert to voice message sent. You can now delete the request.');
        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }
}
