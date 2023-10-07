<?php

namespace App\Http\Livewire\Pilots;

use App\Models\ClxMessage;
use App\Models\RclMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotifyNewEta extends Component
{
    public RclMessage $rclMessage;
    public $entryTime;

    public function mount()
    {
        $this->rclMessage = RclMessage::whereVatsimAccountId(Auth::id())->latest()->first();
    }

    public function render()
    {
        return view('livewire.pilots.notify-new-eta');
    }

    protected $rules = [
        'entryTime' => 'required|numeric|digits:4',
    ];

    public function submit()
    {
        $this->validate();
    }
}
