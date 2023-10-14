<?php

namespace App\Observers;

use App\Events\CpdlcMessageSentEvent;
use App\Models\CpdlcMessage;

class CpdlcMessageObserver
{
    public function created(CpdlcMessage $cpdlcMessage): void
    {
        CpdlcMessageSentEvent::dispatch($cpdlcMessage->pilot, $cpdlcMessage);
    }
}
