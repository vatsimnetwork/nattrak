<?php

namespace App\Http\Livewire\Controllers;

use App\Enums\ClxCancellationReasons;
use App\Enums\DatalinkAuthorities;
use App\Enums\RclResponsesEnum;
use App\Events\ClxIssuedEvent;
use App\Models\ClxMessage;
use App\Models\DatalinkAuthority;
use App\Models\RclMessage;
use App\Models\Track;
use App\Models\VatsimAccount;
use App\Services\CpdlcService;
use App\Services\VatsimDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Validation\Validator;

class CreateManualClx extends Component
{
    public $isConcorde = false;
    public $tracks;
    public $dlAuthorities;
    public $activeDlAuthority;
    public $callsign;
    public $destination;
    public $flightLevel;
    public $mach;
    public $selectedTrack;
    public $randomRouteing;
    public $entryFix;
    public $entryTime;
    public $tmi;
    public $freeText;

    public function mount()
    {
        $dataService = new VatsimDataService();
        $this->activeDlAuthority = $dataService->getActiveControllerAuthority(Auth::user()) ?? DatalinkAuthority::whereId('NAT')->first();
        $this->tmi = current_tmi();
    }
    public function render()
    {
        return view('livewire.controllers.create-manual-clx');
    }

    public function updatedSelectedTrack($value)
    {
        if ($value == '' || $this->isConcorde) {
            $this->entryFix = "";
            return;
        }

        $track = Track::whereId($value)->first();
        if (in_array($track->identifier, ["SN", "SO", "SM"])) {
            $this->entryFix = "";
            return;
        }
        $this->entryFix = strtok($track->last_routeing, " ");
    }

    protected $messages = [
        'mach.regex' => 'Mach must be in format 0xx (e.g. .74 = 074)',
        'flight_level.max' => 'You must file a valid flight level.',
        'callsign.alpha_num' => 'Your callsign must be valid with no spaces as you would enter it into your pilot client. E.g. BAW14LA, AAL134',
    ];

    protected function rules(): array
    {
        return [
            'callsign' => 'required|string|max:7|alpha_num',
            'destination' => 'required|string|min:4|max:4|alpha',
            'flightLevel' => 'required|numeric|digits:3|min:055',
            'mach' => 'required|numeric|digits:3',
            'entryFix' => 'required|max:5',
            'entryTime' => 'required|numeric|digits:4',
            'tmi' => 'required|numeric|min:001|max:366',
            'randomRouteing' => 'nullable|regex:/^[A-Z\/0-9 _]*[A-Z\/0-9][A-Z\/0-9 _]*$/',
        ];
    }

    protected function prepareForValidation($attributes): array
    {
        if (empty($attributes['entryFix']) && !empty($attributes['selectedTrack'])) {
            $attributes['entryFix'] = strtok(Track::whereId($attributes['selectedTrack'])->firstOrFail()->last_routeing, ' ');
        }
        return $attributes;
    }

    public function submit()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                /**
                 * Track/RR check
                 */
                if ($this->selectedTrack != null && $this->randomRouteing != null) {
                    $validator->errors()->add('select_one_routeing', 'You can only request either a NAT track or a random routeing. Check which one you are allocated in your CTP booking. (NAT Tracks are identified by a letter.)');
                } elseif ($this->selectedTrack == null && $this->randomRouteing == null) {
                    $validator->errors()->add('select_one_routeing', 'You need to request either a NAT track or a random routeing. Check which one you are allocated in your CTP booking. (NAT Tracks are identified by a letter.)');
                }
                if (! $this->isConcorde) {
                    /** Mach regex */
                    if (preg_match("/\b[0][1-9][0-9]\b/", $this->mach) == 0) {
                        $validator->errors()->add('mach.regex', 'Mach must be in format 0xx (e.g. .74 = 074)');
                    }
                }
            });
        })->validate();

        $pilotCid = system_user_id();

        $dataService = new VatsimDataService();
        $flightPlan = $dataService->getVatsimAccountByCallsign($this->callsign);
        if ($flightPlan) {
            if (! VatsimAccount::whereId($flightPlan->cid)->exists()) {
                VatsimAccount::create([
                    'id' => $flightPlan->cid,
                    'given_name' => $flightPlan->name,
                    'surname' => '(AUTOMATIC)',
                    'rating_int' => $flightPlan->pilot_rating,
                    'access_level' => 0,
                ]);
            }
            $pilotCid = $flightPlan->cid;
        }

        $rclMessage = new RclMessage([
            'callsign' => strtoupper($this->callsign),
            'destination' => strtoupper($this->destination),
            'mach' => $this->mach,
            'flight_level' => $this->flightLevel,
            'max_flight_level' => 999,
            'track_id' => !empty($this->selectedTrack) ? $this->selectedTrack : null,
            'random_routeing' => !empty($this->randomRouteing) ? $this->randomRouteing : null,
            'entry_fix' => $this->entryFix,
            'entry_time' => $this->entryTime,
            'tmi' => current_tmi(),
            'free_text' => $this->freeText,
            'vatsim_account_id' => $pilotCid,
            'request_time' => now(),
            'atc_rejected' => false,
            'is_concorde' => false,
        ]);
        $rclMessage->edit_lock = true;
        $rclMessage->edit_lock_time = now();
        $rclMessage->edit_lock_vatsim_account_id = Auth::id();
        $rclMessage->save();

        $cpdlcService = new CpdlcService();
        $cpdlcService->sendMessage(
            author: $this->activeDlAuthority,
            recipient: $rclMessage->callsign,
            recipientAccount: $rclMessage->vatsimAccount,
            message: "** RCL/CLX AT TIME {$rclMessage->created_at->format('H:i')} MANUALLY ENTERED BY ATC **",
            caption: 'ATC have manually created your oceanic clearance request and clearance based on information provided to them.'
        );

        $clxMessage = new ClxMessage([
            'vatsim_account_id' => Auth::id(),
            'rcl_message_id' => $rclMessage->id,
            'flight_level' => $rclMessage->flight_level,
            'mach' => $rclMessage->mach,
            'entry_fix' => $rclMessage->entry_fix,
            'entry_time_restriction' => null, //TODO implement
            'raw_entry_time_restriction' => $rclMessage->entry_time,
            'free_text' => '** RCL/CLX MANUALLY ENTERED BY ATC **',
            'datalink_authority_id' => $this->activeDlAuthority->id,
            'is_concorde' => $rclMessage->is_concorde,
        ]);
        if ($rclMessage->trac) {
            $clxMessage->track_id = $rclMessage->track->id;
            $clxMessage->random_routeing = null;
        } elseif ($rclMessage->random_routeing) {
            $clxMessage->random_routeing = $rclMessage->random_routeing;
            $clxMessage->track_id = null;
        }

        $array = [
            'CLX '.now()->format('Hi dmy').' '.$clxMessage->datalinkAuthority->id.' CLRNCE '.$clxMessage->id,
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
            $msg = "{$clxMessage->datalinkAuthority->name} clears {$rclMessage->callsign} to {$rclMessage->destination} via {$clxMessage->entry_fix}, track {$clxMessage->track->identifier}. From {$clxMessage->entry_fix} maintain Flight Level {$clxMessage->flight_level}, Mach {$clxMessage->mach}.";
        } else {
            $msg = "{$clxMessage->datalinkAuthority->name} clears {$rclMessage->callsign} to {$rclMessage->destination} via {$clxMessage->entry_fix}, random routeing {$clxMessage->random_routeing}. From {$clxMessage->entry_fix} maintain Flight Level {$clxMessage->flight_level}, Mach {$clxMessage->mach}.";
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
            ->log('CLX Message Transmitted By '.$clxMessage->datalinkAuthority->name);

        flashAlert(type: 'success', title: null, message: 'Clearance transmitted.', toast: true, timer: true);

        return redirect()->route('controllers.clx.show-rcl-message', $rclMessage);
    }
}
