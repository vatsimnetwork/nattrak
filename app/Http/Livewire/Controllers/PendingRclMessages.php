<?php

namespace App\Http\Livewire\Controllers;

use App\Models\RclMessage;
use App\Models\Track;
use Livewire\Component;

class PendingRclMessages extends Component
{
    public $tracks;

    public function render()
    {
        $pendingRclMsgs = collect();
        foreach ($this->tracks as $track) {
            $trackMsgs = RclMessage::pending()->when($track != "RR", function ($query) use ($track) {
                $query->where('track_id', Track::whereIdentifier($track)->firstOrFail()->id);
            }, function ($query) {
                $query->where('track_id', null);
            })->orderBy('request_time')->get();
            foreach ($trackMsgs as $msg) { $pendingRclMsgs->add($msg); }
        }

        return view('livewire.controllers.pending-rcl-messages', [
            'pendingRclMsgs' => $pendingRclMsgs
        ]);
    }
}
