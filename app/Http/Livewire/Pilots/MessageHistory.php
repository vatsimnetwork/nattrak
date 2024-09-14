<?php

namespace App\Http\Livewire\Pilots;

use App\Models\CpdlcMessage;
use App\Models\RclMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageHistory extends Component
{
    public $rclMessages;

    public $clxMessages;

    public Collection $cpdlcMessages;

    public $lastPollTime;

    public function mount()
    {
        /**
         * Populate previous RCL messages
         */
        $this->rclMessages = RclMessage::whereVatsimAccountId(Auth::id())->get()->load('clxMessages');

        /**
         * Populate previous CPDLC messages (collection)
         */
        $this->cpdlcMessages = collect();
        $cpdlcMessages = CpdlcMessage::wherePilotId(Auth::id())->orderByDesc('created_at')->get();
        foreach ($cpdlcMessages as $msg) {
            $this->cpdlcMessages->add($msg->toMessageHistoryFormat());
        }

        /**
         * Populate previous CLX messages (collection)
         */
        $this->clxMessages = collect();
        foreach ($this->rclMessages as $rclMessage) {
            foreach ($rclMessage->clxMessages->sortByDesc('created_at') as $clxMessage) {
                $this->clxMessages->add($clxMessage->toMessageHistoryFormat());
            }
        }
    }

    public function getListeners()
    {
        $id = auth()->id();

        return [
            "echo-private:clearance.{$id},.clx.issued" => 'addNewClx',
            "echo-private:cpdlc.{$id},.cpdlc.sent" => 'addNewCpdlc',
        ];
    }

    public function render()
    {
        return view('livewire.pilots.message-history');
    }

    public function addNewClx($data)
    {
        $this->clxMessages->add($data);
        $this->clxMessages = $this->clxMessages->sortByDesc('created_at');
        $this->dispatch('clx-received', dl: 'Clearance received: '.$data['datalink_message'][0]);
    }

    public function addNewCpdlc($data)
    {
        $this->cpdlcMessages->add($data);
        $this->cpdlcMessages = $this->cpdlcMessages->sortBy('created_at');

        $this->dispatch('cpdlc-received', dl:  'Message received from '.$data['datalink_authority']['description']);
    }

    /*public function pollForMessages()
    {
        if ($this->rclMessages->count() > 0) {
            foreach ($this->rclMessages as $rclMessage) {
                if ($rclMessage->clxMessages->count() > 0) {
                    foreach ($rclMessage->clxMessages->sortByDesc('created_at') as $clxMessage) {
                        if (! $this->clxMessages->contains('id', $clxMessage->id)) {
                            $this->clxMessages->add($clxMessage);
                            $this->clxMessages->sortByDesc('created_at');
                            $this->dispatch('clx-received', ['dl' => 'Clearance received: '.$clxMessage->datalink_message[0]]);
                        }
                    }
                }
            }
        }

        $newCpdlcMessages = CpdlcMessage::where('created_at', '>', $this->lastPollTime)->where('pilot_id', Auth::id())->get();
        foreach ($newCpdlcMessages as $message) {
            $this->cpdlcMessages->add($message);
            $this->dispatch('cpdlc-received', ['dl' => 'CPDLC message received: '.$message->free_text]);
        }

        $this->lastPollTime = now();
    }*/
}
