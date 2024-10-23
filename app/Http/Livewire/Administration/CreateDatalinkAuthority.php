<?php

namespace App\Http\Livewire\Administration;

use App\Models\DatalinkAuthority;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateDatalinkAuthority extends Component
{
    #[Validate('required|min:4')]
    public $authorityId = "";

    #[Validate('required')]
    public $name = "";

    #[Validate('required|min:4|max:4')]
    public $prefix = "";

    public $autoAcknowledgeParticipant = false;
    public $validRclTarget = false;
    public $system = false;

    public function render()
    {
        return view('livewire.administration.create-datalink-authority');
    }

    public function save()
    {
        $this->validate();

        DatalinkAuthority::create([
            'id' => $this->authorityId,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'auto_acknowledge_participant' => $this->autoAcknowledgeParticipant,
            'valid_rcl_target' => $this->validRclTarget,
            'system' => $this->system,
        ]);

        flashAlert(type: 'info', title: "Created", message: null, toast: false, timer: false);

        return redirect()->route('administration.datalink-authorities');
    }
}
