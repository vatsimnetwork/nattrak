<?php

namespace App\Http\Controllers;

use App\Enums\ClxCancellationReasons;
use App\Enums\DatalinkAuthorities;
use App\Enums\RclResponsesEnum;
use App\Events\ClxIssuedEvent;
use App\Http\Requests\ClxMessageRequest;
use App\Models\ClxMessage;
use App\Models\DatalinkAuthority;
use App\Models\RclMessage;
use App\Models\Track;
use App\Services\CpdlcService;
use App\Services\VatsimDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClxMessagesController extends Controller
{
    public VatsimDataService $dataService;

    public CpdlcService $cpdlcService;

    /**
     * Initialises VATSIM data service for grabbing active authority and CPDLC message service
     *
     * @param  VatsimDataService  $dataService
     * @param  CpdlcService  $cpdlcService
     */
    public function __construct(VatsimDataService $dataService, CpdlcService $cpdlcService)
    {
        $this->dataService = $dataService;
        $this->cpdlcService = $cpdlcService;
    }

    public function getPending(Request $request)
    {
        return view('controllers.clx.pending', [
            'displayed' => $request->get('display'),
            'tracks' => Track::active()->get(),
            '_pageTitle' => $request->get('display') ? 'Tracks '.implode(', ', $request->get('display')) : 'None selected',
        ]);
    }

    public function getProcessed(Request $request)
    {
        return view('controllers.clx.processed', [
            'displayed' => $request->get('display'),
            'tracks' => Track::active()->get(),
            '_pageTitle' => $request->get('display') ? 'Tracks '.implode(', ', $request->get('display')) : 'None selected',
        ]);
    }

    public function getProcessedViaClxModels(Request $request)
    {
        $display = $request->get('display') ?? [];
        $messages = collect();

        foreach ($display as $id) {
            $messagesOnTrack = ClxMessage::where('overwritten', false)
                ->when(in_array($id, ['RR', 'CONC']) == false, function ($query) use ($id) {
                    $query->where('track_id', Track::whereIdentifier($id)->firstOrFail()->id);
                }, function ($query) use ($id) {
                    if ($id == 'RR') {
                        $query->where('track_id', null);
                    } elseif ($id == 'CONC') {
                        $query->where('is_concorde', true);
                    }
                })
                ->with('rclMessage')
                ->orderByDesc('created_at')
                ->get();

            foreach ($messagesOnTrack as $message) {
                $messages->add($message);
            }
        }

        return view('controllers.clx.processed', [
            'displayed' => $display,
            'tracks' => Track::active()->get(),
            'messages' => $messages,
            '_pageTitle' => $display ? 'Tracks '.implode(', ', $display) : 'Select tracks',
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
            'dlAuthorities' => DatalinkAuthority::notSystem()->get(),
            'tracks' => $rclMessage->is_concorde ? Track::concorde()->get() : Track::active()->get(),
            'activeDlAuthority' => $this->dataService->getActiveControllerAuthority(Auth::user()) ?? DatalinkAuthority::find('NAT'),
            '_pageTitle' => $rclMessage->callsign,
        ]);
    }

    public function showClxMessage(ClxMessage $clxMessage)
    {
        return redirect()->route('controllers.clx.show-rcl-message', $clxMessage->rcl_message_id);
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
                flashAlert(type: 'error', title: 'Track not found.', message: null, toast: false, timer: false);

                return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
            }
            $newEntryFix = strtok($newTrack->last_routeing, ' ');
        } elseif ($request->filled('new_random_routeing')) {
            $newEntryFix = strtok($request->get('new_random_routeing'), ' ');
        }

        /**
         * Formulate entry time requirement if needed..
         */
        $entryRequirement = null;
        if ($request->filled('entry_time_type') && $request->filled('entry_time_requirement')) {
            if ($request->get('entry_time_type') != 'none') {
                $entryRequirement = "{$request->get('entry_time_type')}{$request->get('entry_time_requirement')}";
            }
        }

        $datalinkAuthority = DatalinkAuthority::find($request->get('datalink_authority'));

        /**
         * Create the message
         */
        $clxMessage = new ClxMessage([
            'vatsim_account_id' => $request->user()->id,
            'rcl_message_id' => $rclMessage->id,
            'flight_level' => $request->filled('atc_fl') ? $request->get('atc_fl') : $rclMessage->flight_level,
            'upper_flight_level' => $rclMessage->upper_flight_level ? ($request->get('atc_ufl') ?? $rclMessage->upper_flight_level) : null,
            'mach' => $request->filled('atc_mach') ? $request->get('atc_mach') : $rclMessage->mach,
            'entry_fix' => $newEntryFix ?? $rclMessage->entry_fix,
            'entry_time_restriction' => $entryRequirement ?? null,
            'cto_time' => $request->filled('cto_time') ? $request->get('cto_time') : $rclMessage->entry_time,
            'raw_entry_time_restriction' => $request->get('entry_time_requirement'),
            'free_text' => $isReclearance ? '** RECLEARANCE '.now()->format('Hi').' ** '.$request->get('free_text') : $request->get('free_text'),
            'datalink_authority_id' => $datalinkAuthority->id,
            'is_concorde' => $rclMessage->is_concorde,
        ]);

        /**
         * Assign track or RR
         */
        if ($rclMessage->track || $newTrack) {
            $clxMessage->track_id = $newTrack ? $newTrack->id : $rclMessage->track->id;
            $clxMessage->random_routeing = null;
        } elseif ($rclMessage->random_routeing || $request->filled('new_random_routeing')) {
            $clxMessage->random_routeing = $request->filled('new_random_routeing') ? $request->get('new_random_routeing') : $rclMessage->random_routeing;
            $clxMessage->track_id = null;
        }

        /**
         * Create datalink messages
         */
        $array = [
            'CLX '.now()->format('Hi dmy').' '.$datalinkAuthority->id.' CLRNCE '.$clxMessage->id,
            $rclMessage->callsign.' CLRD TO '.$rclMessage->destination.' VIA '.$clxMessage->entry_fix,
            $clxMessage->track ? 'NAT '.$clxMessage->track->identifier : 'RANDOM ROUTE',
            $clxMessage->track ? $clxMessage->track->last_routeing : $clxMessage->random_routeing,
        ];
        if ($rclMessage->is_concorde) {
            $array[] = 'FM '.$clxMessage->entry_fix.'/'.$rclMessage->entry_time.' MNTN BLOCK LOWER F'.$clxMessage->flight_level.' UPPER F'.$clxMessage->upper_flight_level.' M'.$clxMessage->mach;
        } else {
            $array[] = 'FM '.$clxMessage->entry_fix.'/'.$rclMessage->entry_time.' MNTN F'.$clxMessage->flight_level.' M'.$clxMessage->mach;
        }
        // Only show crossing restriction if entry time =/= the restriction due to the bodge
        if ($clxMessage->entry_time_restriction && ($clxMessage->raw_entry_time_restriction != $rclMessage->entry_time)) {
            $array[] = "/ATC CROSS {$clxMessage->entry_fix} {$clxMessage->formatEntryTimeRestriction()}";
        }
        if (($clxMessage->mach != $rclMessage->mach) || ($rclMessage->latestClxMessage && ($clxMessage->mach != $rclMessage->latestClxMessage->mach))) {
            $array[] = '/ATC SPEED CHANGED';
        }
        if (($clxMessage->flight_level != $rclMessage->flight_level) || ($rclMessage->latestClxMessage && ($clxMessage->flight_level != $rclMessage->latestClxMessage->flight_level))) {
            $array[] = '/ATC FLIGHT LEVEL CHANGED';
        }
        if ($clxMessage->routeing_changed) {
            if ($clxMessage->track) {
                $array[] = '/ROUTE CHANGED TO TRACK '.$clxMessage->track->identifier;
            } else {
                $array[] = '/ROUTE CHANGED TO RANDOM ROUTEING';
            }
        }
        if ($clxMessage->free_text) {
            $array[] = '/ATC '.strtoupper($clxMessage->free_text);
        }
        $array[] = 'END OF MESSAGE';
        $clxMessage->datalink_message = $array;
        $msg = '';
        if ($clxMessage->track) {
            $msg = "{$datalinkAuthority->name} clears {$rclMessage->callsign} to {$rclMessage->destination} via {$clxMessage->entry_fix}, track {$clxMessage->track->identifier}. From {$clxMessage->entry_fix} maintain Flight Level {$clxMessage->flight_level}, Mach {$clxMessage->mach}.";
        } else {
            $msg = "{$datalinkAuthority->name} clears {$rclMessage->callsign} to {$rclMessage->destination} via {$clxMessage->entry_fix}, random routeing {$clxMessage->random_routeing}. From {$clxMessage->entry_fix} maintain Flight Level {$clxMessage->flight_level}, Mach {$clxMessage->mach}.";
        }
        // Only show crossing restriction if entry time =/= the restriction due to the bodge
        if ($clxMessage->entry_time_restriction && ($clxMessage->raw_entry_time_restriction != $rclMessage->entry_time)) {
            $msg .= " Cross {$clxMessage->entry_fix} ".strtolower($clxMessage->formatEntryTimeRestriction()).'.';
        }
        if (($clxMessage->mach != $rclMessage->mach) || ($rclMessage->latestClxMessage && ($clxMessage->mach != $rclMessage->latestClxMessage->mach))) {
            $msg .= ' Speed changed.';
        }
        if (($clxMessage->flight_level != $rclMessage->flight_level) || ($rclMessage->latestClxMessage && ($clxMessage->flight_level != $rclMessage->latestClxMessage->flight_level))) {
            $msg .= ' Flight level changed.';
        }
        if ($clxMessage->free_text) {
            $msg .= " {$clxMessage->free_text}";
        }
        $clxMessage->simple_datalink_message = $msg;

        /**
         * Save
         */
        $clxMessage->save();

        //If there is a previous CLX, override it first
        if ($rclMessage->latestClxMessage) {
            $rclMessage->latestClxMessage->update([
                'overwritten' => true,
                'overwritten_by_clx_message_id' => $rclMessage->latestClxMessage->id,
                'cancelled' => true,
                'cancellation_reason' => ClxCancellationReasons::Superseded
            ]);
        }

        /**
         * Assign Clx message to Rcl
         */
        $rclMessage->clx_message_id = $clxMessage->id;
        $rclMessage->save();

        ClxIssuedEvent::dispatch($rclMessage->vatsimAccount, $clxMessage);

        activity('datalink')
            ->causedBy($clxMessage->vatsimAccount)
            ->performedOn($rclMessage)
            ->withProperties(['datalink' => $clxMessage->data_link_message])
            ->log('CLX Message Transmitted By '.$datalinkAuthority->id);

        flashAlert(type: 'success', title: null, message: 'Clearance transmitted.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }

    public function deleteRclMessage(Request $request, RclMessage $rclMessage)
    {
        $redirectToProcessed = $rclMessage->clxMessages->count() > 0;
        $rclMessage->delete();
        flashAlert(type: 'success', title: null, message: 'Request deleted.', toast: true, timer: true);
        if ($redirectToProcessed) {
            return redirect()->route('controllers.clx.processed');
        } else {
            return redirect()->route('controllers.clx.pending');
        }
    }

    public function revertToVoice(Request $request, RclMessage $rclMessage)
    {
        $datalinkAuthority = $this->dataService->getActiveControllerAuthority ?? DatalinkAuthority::find('NAT');
        $this->cpdlcService->sendMessage(
            author: $datalinkAuthority,
            recipient: $rclMessage->callsign,
            recipientAccount: $rclMessage->vatsimAccount,
            message: sprintf(RclResponsesEnum::Contact->value, strtoupper($datalinkAuthority->description())),
            caption: RclResponsesEnum::Contact->text()
        );

        flashAlert(type: 'success', title: null, message: 'Revert to voice message sent. You can delete the request now.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }

    public function moveToProcessed(Request $request, RclMessage $rclMessage)
    {
        $datalinkAuthority = $this->dataService->getActiveControllerAuthority ?? DatalinkAuthority::find('NAT');
//        $this->cpdlcService->sendMessage(
//            author: $datalinkAuthority,
//            recipient: $rclMessage->callsign,
//            recipientAccount: $rclMessage->vatsimAccount,
//            message: sprintf(RclResponsesEnum::AcknowledgeMoved->value, strtoupper($datalinkAuthority->description())),
//            caption: RclResponsesEnum::Acknowledge->text()
//        );

        $clxMessage = new ClxMessage([
            'vatsim_account_id' => $request->user()->id,
            'rcl_message_id' => $rclMessage->id,
            'flight_level' => $rclMessage->flight_level,
            'upper_flight_level' => $rclMessage->upper_flight_level ? $rclMessage->upper_flight_level : null,
            'mach' => $rclMessage->mach,
            'entry_fix' => $rclMessage->entry_fix,
            'entry_time_restriction' => null,
            'raw_entry_time_restriction' => $rclMessage->entry_time,
            'free_text' => "** AUTO ACKNOWLEDGE **",
            'datalink_authority_id' => $datalinkAuthority->id,
            'is_concorde' => $rclMessage->is_concorde,
            'simple_datalink_message' => '** AUTO ACKNOWLEDGED REFER RCL REQUEST **',
            'datalink_message' => ['** AUTO ACKNOWLEDGED REFER RCL REQUEST **'],
        ]);

        /**
         * Assign track or RR
         */
        if ($rclMessage->track) {
            $clxMessage->track_id = $rclMessage->track->id;
            $clxMessage->random_routeing = null;
        } elseif ($rclMessage->random_routeing) {
            $clxMessage->random_routeing = $rclMessage->random_routeing;
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

        //ClxIssuedEvent::dispatch($rclMessage->vatsimAccount, $clxMessage);

        activity('datalink')
            ->causedBy($clxMessage->vatsimAccount)
            ->performedOn($rclMessage)
            ->withProperties(['datalink' => $clxMessage->data_link_message])
            ->log('CLX Message Transmitted By '.$datalinkAuthority->name);

        flashAlert(type: 'success', title: null, message: 'Clearance moved.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }

    public function create()
    {
        return view('controllers.clx.rcl-messages.create', [
            'dlAuthorities' => DatalinkAuthorities::cases(),
            'tracks' => Track::where('active', true)->orWhere('concorde', true)->get(),
            'activeDlAuthority' => $this->dataService->getActiveControllerAuthority(Auth::user()) ?? DatalinkAuthority::find('NAT'),
            '_pageTitle' => 'Create Manual Clearance',
        ]);
    }
}
