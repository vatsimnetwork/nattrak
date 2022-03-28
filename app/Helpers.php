<?php

use App\Services\VatsimDataService;

function current_tmi(): string
{
    $dataService = new VatsimDataService();
    return $dataService->getTmi() ?? 'N/A';
}
