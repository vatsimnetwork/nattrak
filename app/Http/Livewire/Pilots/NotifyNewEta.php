<?php

namespace App\Http\Livewire\Pilots;

use App\Enums\ClxCancellationReasons;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class NotifyNewEta extends Component
{
    public RclMessage $rclMessage;
    public ClxMessage|null $latestClxMessage;
    public $entryTime;
    public bool $reject = false;

    public function mount()
    {
        $rcl = Auth::user()->rclMessages->sortByDesc('created_at')->first();
        if (!$rcl) {
            $this->reject = true;
        } else {
            $this->rclMessage = $rcl;
            $this->latestClxMessage = $rcl->latestClxMessage;
        }
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

        DB::transaction(function(): void {
            $this->rclMessage->update([
                'previous_clx_message' => $this->latestClxMessage->toArray(),
                'new_entry_time' => true,
                'new_entry_time_notified_at' => now(),
                'previous_entry_time' => $this->rclMessage->entry_time,
                'entry_time' => $this->entryTime
            ]);
            $this->rclMessage->clx_message_id = null;
            $this->rclMessage->save();

            $this->latestClxMessage->update([
                'cancelled' => true,
                'cancellation_reason' => ClxCancellationReasons::NewEta
            ]);
            $this->latestClxMessage->save();
        });

        flashAlert(type: 'success', title: 'Success!', message: 'New ETA submitted. Keep an eye on your message history page!', toast: false, timer: false);
        return redirect()->route('pilots.message-history');
    }
}
