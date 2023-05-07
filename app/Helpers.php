<?php

use App\Enums\DatalinkAuthorities;
use App\Services\VatsimDataService;
use Illuminate\Support\Facades\Auth;

function current_tmi(): string
{
    $dataService = new VatsimDataService();

    return $dataService->getTmi() ?? 'N/A';
}

function current_dl_authority(): ?DatalinkAuthorities
{
    $dataService = new VatsimDataService();

    return $dataService->getActiveControllerAuthority(Auth::user()) ?? null;
}

function flashAlert(string $type, ?string $title, ?string $message, bool $toast, bool $timer): void
{
    \Illuminate\Support\Facades\Session::flash('alert', [
        'icon' => $type,
        'title' => $title ?? '',
        'text' => $message ?? '',
        'toast' => $toast,
        'timer' => $timer ? 3000 : null,
    ]);
}
