<?php

namespace App\Http\Livewire\ApiTokens;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ManageAccountToken extends Component
{
    public function render()
    {
        return view('livewire.api-tokens.manage-account-token')
            ->layout('_layouts.main')
            ->section('page');
    }

    public function generateToken()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        $token = $user->createToken(
            $user->id, ['nattrak:plugin'], now()->plus(days: 365)
        );


    }
}
