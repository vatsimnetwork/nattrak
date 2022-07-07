<?php

namespace App\Http\Livewire\Pilots;

use App\Models\ClxMessage;
use App\Models\CpdlcMessage;
use App\Models\RclMessage;
use Illuminate\Database\Eloquent\Collection as ECollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MessageHistory extends Component
{
    public $rclMessages;
    public $clxMessages;
    public $cpdlcMessages;
    public $lastPollTime;

    public function mount()
    {
        /**
         * Record current time
         */
        $this->lastPollTime = now();

        /**
         * Populate previous RCL messages
         */
        $this->rclMessages = RclMessage::whereVatsimAccountId(Auth::id())->get();

        /**
         * Populate previous CPDLC messages (collection)
         */
        $this->cpdlcMessages = collect();
        $cpdlcMessages = CpdlcMessage::wherePilotId(Auth::id())->orderByDesc('created_at')->get();
        foreach ($cpdlcMessages as $msg) {
            $this->cpdlcMessages->add($msg);
        }

        /**
         * Populate previous CLX messages (collection)
         */
        $this->clxMessages = collect();
        foreach ($this->rclMessages as $rclMessage)
        {
            foreach ($rclMessage->clxMessages->sortByDesc('created_at') as $clxMessage) {
                $this->clxMessages->add($clxMessage);
            }
        }
    }

    public function render()
    {
        return view('livewire.pilots.message-history');
    }

    public function pollForMessages()
    {
        if ($this->rclMessages->count() > 0) {
            foreach ($this->rclMessages as $rclMessage) {
                if ($rclMessage->clxMessages->count() > 0) {
                    foreach ($rclMessage->clxMessages->sortByDesc('created_at') as $clxMessage) {
                        if (!$this->clxMessages->contains('id', $clxMessage->id)) {
                            $this->clxMessages->add($clxMessage);
                            $this->clxMessages->sortByDesc('created_at');
                            $this->dispatchBrowserEvent('clx-received', ['dl' => 'Clearance received: ' . $clxMessage->datalink_message[0]]);
                        }
                    }
                }
            }
        }

        $newCpdlcMessages = CpdlcMessage::where('created_at', '>', $this->lastPollTime)->where('pilot_id', Auth::id())->get();
        foreach ($newCpdlcMessages as $message) {
            $this->cpdlcMessages->add($message);
            $this->dispatchBrowserEvent('cpdlc-received', ['dl' => 'CPDLC message received: ' . $message->free_text]);
        }

        $this->lastPollTime = now();
    }
}
