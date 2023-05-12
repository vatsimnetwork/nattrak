<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('clearance.{accountId}', function (App\Models\VatsimAccount $account, int $accountId) {
    return $account->id === $accountId;
});

Broadcast::channel('cpdlc.{accountId}', function (App\Models\VatsimAccount $account, int $accountId) {
    return $account->id === $accountId;
});
