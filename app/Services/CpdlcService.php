<?php

namespace App\Services;

use App\Enums\DatalinkAuthorities;
use App\Models\CpdlcMessage;
use App\Models\DatalinkAuthority;
use App\Models\VatsimAccount;

class CpdlcService
{
    /**
     * @param  DatalinkAuthorities  $author
     * @param  string  $recipient
     * @param  VatsimAccount  $recipientAccount
     * @param  string  $message
     * @return CpdlcMessage
     */
    public function sendMessage(DatalinkAuthority $author, string $recipient, VatsimAccount $recipientAccount, string $message, ?string $caption): CpdlcMessage
    {
        return CpdlcMessage::create([
            'datalink_authority_id' => $author->id,
            'pilot_id' => $recipientAccount->id,
            'pilot_callsign' => $recipient,
            'message' => $message,
            'caption' => $caption ?? null,
        ]);
    }
}
