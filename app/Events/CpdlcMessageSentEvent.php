<?php

namespace App\Events;

use App\Models\CpdlcMessage;
use App\Models\VatsimAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CpdlcMessageSentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $afterCommit = true;

    /**
     * @param  VatsimAccount  $account
     * @param  CpdlcMessage  $cpdlcMessage
     */
    public function __construct(public VatsimAccount $account, public CpdlcMessage $cpdlcMessage)
    {
    }

    public function broadcastAs(): string
    {
        return 'cpdlc.sent';
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->cpdlcMessage->toMessageHistoryFormat();
    }

    /**
     * {@inheritDoc}
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('cpdlc.'.$this->account->id),
        ];
    }
}
