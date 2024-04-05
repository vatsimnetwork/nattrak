<?php

namespace App\Http\Livewire\Controllers;

use Livewire\Component;

class CreateManualClx extends Component
{
    public $isConcorde = false;
    public $tracks;
    public $dlAuthorities;
    public $activeDlAuthority;

    public $callsign;
    public $destination;
    public $flightLevel;
    public $upperFlightLevel;
    public $maxFlightLevel;
    public $mach;
    public $selectedTrack;
    public $randomRouteing;
    public $entryFix;
    public $entryTime;
    public $tmi;
    public $freeText;

    public function mount()
    {
        $this->tmi = current_tmi();
    }
    public function render()
    {
        return view('livewire.controllers.create-manual-clx');
    }
}
