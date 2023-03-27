<?php

use App\Services\VatsimDataService;

function current_tmi(): string
{
    $dataService = new VatsimDataService();

    return $dataService->getTmi() ?? 'N/A';
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
