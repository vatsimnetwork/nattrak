<?php

namespace App\Http\Livewire\Controllers;

use App\Enums\ClxCancellationReasons;
use App\Enums\DatalinkAuthorities;
use App\Enums\DomesticAuthorities;
use App\Models\RclMessage;
use App\Services\CpdlcService;
use App\Services\VatsimDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class NotifyNewEtaForPilot extends Component
{
    public $entryTime;
    public $callsign;
    public $domesticAuthorities;
    public $activeDomesticAuthority;
    public bool $reject = false;

    public function mount()
    {
        $this->domesticAuthorities = DomesticAuthorities::cases();
        $dataService = new VatsimDataService();
        $this->activeDomesticAuthority = $dataService->getActiveDomesticControllerAuthority(Auth::user())->value ?? DomesticAuthorities::UNKN->value;
    }

    public function render()
    {
        return view('livewire.controllers.notify-new-eta-for-pilot');
    }

    protected $rules = [
        'entryTime' => 'required|numeric|digits:4',
        'callsign' => 'required|exists:rcl_messages,callsign'
    ];

    public function submit()
    {
        $this->validate();

        DB::transaction(function () {
            $rcl = RclMessage::whereCallsign($this->callsign)->first();
            $clx = null;
            if ($rcl->latestClxMessage) {
                $clx = $rcl->latestClxMessage;
            }
            $rcl->update([
                'previous_clx_message' => $rcl->latestClxMessage?->toArray(),
                'new_entry_time' => true,
                'new_entry_time_notified_at' => now(),
                'previous_entry_time' => $rcl->entry_time,
                'entry_time' => $this->entryTime,
            ]);
            $rcl->clx_message_id = null;
            $rcl->save();

            if ($clx) {
                $clx->update([
                    'cancelled' => true,
                    'cancellation_reason' => ClxCancellationReasons::NewEta
                ]);
                $clx->save();
            }

            $cpdlcService = new CpdlcService();
            $cpdlcService->sendMessage(
                author: DatalinkAuthorities::SYS,
                recipient: $rcl->callsign,
                recipientAccount: $rcl->vatsimAccount,
                message: "** NEW ENTRY TIME {$this->entryTime} NOTIFIED BY DOMESTIC {$this->activeDomesticAuthority} **",
                caption: 'A domestic controller has notified oceanic control of your new estimated oceanic entry time.'
            );
        });

        flashAlert(type: 'success', title: 'Success!', message: 'New ETA submitted', toast: false, timer: false);
        return redirect()->route('domestic.notify-new-eta-for-pilot');
    }
}
