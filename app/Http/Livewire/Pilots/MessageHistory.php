<?php

namespace App\Http\Livewire\Pilots;

use App\Models\ClxMessage;
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

    public function mount()
    {
        $this->rclMessages = RclMessage::whereVatsimAccountId(Auth::id())->get();
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

    public function pollForClx()
    {
        if ($this->rclMessages->count() == 0) {
            return;
        }
        Log::info('this->rcl > 0');

        foreach ($this->rclMessages as $rclMessage) {
            if ($rclMessage->clxMessages->count() > 0) {
                foreach ($rclMessage->clxMessages->sortByDesc('created_at') as $clxMessage) {
                    if (! $this->clxMessages->contains('id', $clxMessage->id)) {
                        $this->clxMessages->add($clxMessage);
                        $this->clxMessages->sortByDesc('created_at');
                        $this->dispatchBrowserEvent('clx-received', ['dl' => 'Clearance received: ' . $clxMessage->dataLinkMessage[0]]);
                    } else {
                        Log::info('exists');
                    }
                }
            }
        }
    }
}
