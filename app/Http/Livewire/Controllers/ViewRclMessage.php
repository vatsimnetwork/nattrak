<?php

namespace App\Http\Livewire\Controllers;

use App\Enums\ClxCancellationReasons;
use App\Enums\RclResponsesEnum;
use App\Events\ClxIssuedEvent;
use App\Exceptions\InvalidTrackException;
use App\Http\Requests\ClxMessageRequest;
use App\Models\ClxMessage;
use App\Models\CtpBooking;
use App\Models\DatalinkAuthority;
use App\Models\RclMessage;
use App\Services\ClxMessageService;
use App\Services\CpdlcService;
use App\Services\VatsimDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ViewRclMessage extends Component
{
    public RclMessage $rclMessage;
    public $datalinkAuthorities;
    public $tracks;
    public DatalinkAuthority $activeDatalinkAuthority;

    public $atcFlightLevel;
    public $atcUpperFlightLevel;
    public $atcMach;
    public $atcFreeText;
    public $atcEntryTimeType;
    public $atcCtoTime;
    public $atcEntryTimeRequirement;
    public $atcNewTrack;
    public $atcNewRandomRouteing;
    public $atcDatalinkAuthority;

    public $declineDeleteReason;

    public ?CtpBooking $ctpBooking;

    protected function rules()
    {
        return [
            'atcFlightLevel' => 'nullable|numeric|digits:3',
            'atcUpperFlightLevel' => 'nullable|numeric|digits:3',
            'atcMach' => 'nullable|numeric|digits:3',
            'atcFreeText' => 'nullable',
            'atcEntryTimeType' => ['nullable', 'required_with:entry_time_requirement', Rule::in(['<', '=', '>'])],
            'atcCtoTime' => 'required|numeric|digits:4',
            'atcEntryTimeRequirement' => 'nullable|required_with:entry_time_type|digits:4|numeric',
            'atcDatalinkAuthority' => 'required',
        ];
    }

    public function mount()
    {
        $this->atcCtoTime = $this->rclMessage->entry_time;
        $this->atcDatalinkAuthority = (new VatsimDataService())->getActiveControllerAuthority(Auth::user())->id ?? DatalinkAuthority::find('NAT')->id;
        $this->atcEntryTimeRequirement = $this->rclMessage->entry_time;

        $this->ctpBooking = CtpBooking::whereCid($this->rclMessage->vatsimAccount->id)->first() ?? null;
    }

    public function ctpBookingCompliant(): bool
    {
        if (!$this->ctpBooking) { return false; }
        if ($this->rclMessage->destination != $this->ctpBooking->destination) { return false; }
        if ($this->rclMessage->flight_level != $this->ctpBooking->flight_level) { return false; }

        return true;
    }

    public function render()
    {
        return view('livewire.controllers.view-rcl-message');
    }

    /**
     * @throws InvalidTrackException
     */
    public function transmitClearance(ClxMessageService $service)
    {
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                if ($this->atcEntryTimeType == 'none') {
                    $this->atcEntryTimeType = null;
                    $this->atcEntryTimeRequirement = null;
                }
                if ($this->atcNewTrack != null && $this->atcNewRandomRouteing != null) {
                    $validator->errors()->add('select_one_routeing', 'ROUTE: Select only NAT or RR');
                }
            });
        })->validate();

        $service = new ClxMessageService();

        $isReclearance = $this->rclMessage->clxMessages->isNotEmpty();
        $formattedEntryTimeAndFix = $this->atcEntryTimeType ? $service->formatEntryTimeRequirement(entryTimeType: $this->atcEntryTimeType, entryTimeRequirement: $this->atcEntryTimeRequirement) : null;

        $newTrackAndEntryFix = null;
        if ($this->atcNewTrack != null || $this->atcNewRandomRouteing != null) {
            $newTrackAndEntryFix = $service->getNewEntryFixOrTrack($this->atcNewTrack, $this->atcNewRandomRouteing);
        }

        $clxToOverride = $this->rclMessage->latestClxMessage?->id;

        $clxMessage = new ClxMessage([
            'vatsim_account_id' => Auth::id(),
            'rcl_message_id' => $this->rclMessage->id,
            'flight_level' => $this->atcFlightLevel ?? $this->rclMessage->flight_level,
            'upper_flight_level' => $this->rclMessage->upper_flight_level ? ($this->atcUpperFlightLevel ?? $this->rclMessage->upper_flight_level) : null,
            'mach' => $this->atcMach ?? $this->rclMessage->mach,
            'entry_fix' => strtoupper($newTrackAndEntryFix ? $newTrackAndEntryFix['newEntryFix'] : $this->rclMessage->entry_fix),
            'entry_time_restriction' => $formattedEntryTimeAndFix ?? null,
            'cto_time' => $this->atcCtoTime ?? $this->rclMessage->entry_time,
            'raw_entry_time_restriction' => $this->atcEntryTimeRequirement,
            'free_text' => $isReclearance ? '** RECLEARANCE '.now()->format('Hi').' ** '.$this->atcFreeText: $this->atcFreeText,
            'datalink_authority_id' => $this->atcDatalinkAuthority,
            'is_concorde' => $this->rclMessage->is_concorde,
        ]);

        if ($this->atcNewTrack) {
            $clxMessage->track_id = $this->atcNewTrack;
            $clxMessage->random_routeing = null;
        }
        else if ($this->atcNewRandomRouteing) {
            $clxMessage->random_routeing = strtoupper($this->atcNewRandomRouteing);
            $clxMessage->track_id = null;
        }
        else {
            if ($this->rclMessage->random_routeing) {
                $clxMessage->random_routeing = $this->rclMessage->random_routeing;
            } else {
                $clxMessage->track_id = $this->rclMessage->track_id;
            }
        }

        $clxMessage->datalink_message = $service->createDatalinkMessage($clxMessage);
        $clxMessage->simple_datalink_message = $service->createSimpleDatalinkMessage($clxMessage);

        $clxMessage->save();


        if ($clxToOverride) {
            ClxMessage::whereId($clxToOverride)->update([
                'overwritten' => true,
                'overwritten_by_clx_message_id' => $clxMessage->id,
                'cancelled' => true,
                'cancellation_reason' => ClxCancellationReasons::Superseded
            ]);
        }

        $this->rclMessage->clx_message_id = $clxMessage->id;
        $this->rclMessage->save();

        ClxIssuedEvent::dispatch($this->rclMessage->vatsimAccount, $clxMessage);

        activity('datalink')
            ->causedBy($clxMessage->vatsimAccount)
            ->performedOn($this->rclMessage)
            ->withProperties(['datalink' => $clxMessage->data_link_message])
            ->log('CLX Message Transmitted By '.$this->atcDatalinkAuthority);

        flashAlert(type: 'success', title: null, message: 'Clearance transmitted.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $this->rclMessage);
    }

    public function revertToVoice()
    {
        $service = new CpdlcService();
        $service->sendMessage(
            author: $this->activeDatalinkAuthority,
            recipient: $this->rclMessage->callsign,
            recipientAccount: $this->rclMessage->vatsimAccount,
            message: sprintf(RclResponsesEnum::Contact->value, strtoupper($this->activeDatalinkAuthority->name) . ' (' . $this->activeDatalinkAuthority->id . ')'),
            caption: RclResponsesEnum::Contact->text()
        );

        flashAlert(type: 'success', title: null, message: 'Revert to voice message sent. You can delete the request now.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $this->rclMessage);
    }

    public function moveToProcessedList()
    {
        $service = new ClxMessageService();
        $service->moveAutoAcknowledgedRclToProcessedList($this->rclMessage, true);

        flashAlert(type: 'success', title: null, message: 'Moved to processed list.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $this->rclMessage);
    }

    public function declineAndDelete()
    {
        $service = new CpdlcService();
        $service->sendMessage(
            author: $this->activeDatalinkAuthority,
            recipient: $this->rclMessage->callsign,
            recipientAccount: $this->rclMessage->vatsimAccount,
            message: sprintf(RclResponsesEnum::Invalid->value, strtoupper($this->declineDeleteReason ?? 'UNKNOWN REASON')),
            caption: RclResponsesEnum::Invalid->text()
        );

        $this->rclMessage->delete();

        flashAlert(type: 'success', title: null, message: 'Message sent and request deleted.', toast: true, timer: true);

        return redirect()->route('controllers.clx.pending');
    }

    public function deleteManual()
    {

    }
}
