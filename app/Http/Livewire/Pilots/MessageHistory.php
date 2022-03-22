<?php

namespace App\Http\Livewire\Pilots;

use App\Models\ClxMessage;
use App\Models\RclMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageHistory extends Component
{
    public function render()
    {
        $rclMessages = RclMessage::whereVatsimAccountId(Auth::id())->get();
        return view('livewire.pilots.message-history', [
            'rclMessages' => $rclMessages
        ]);
    }

    public function booted()
    {
        $this->dispatchBrowserEvent('check-can-notify');
    }
}
