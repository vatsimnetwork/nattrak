<?php

namespace App\Http\Livewire\Controllers;

use App\Models\RclMessage;
use App\Models\Track;
use Livewire\Component;

class PendingRclMessages extends Component
{
    public $track;

    public function render()
    {
        $pendingRclMsgs = RclMessage::pending()->when($this->track, function ($query) {
            $query->whereTrackId($this->track->id);
        })->orderBy('request_time')->get();

        return view('livewire.controllers.pending-rcl-messages', [
            'pendingRclMsgs' => $pendingRclMsgs
        ]);
    }
}
