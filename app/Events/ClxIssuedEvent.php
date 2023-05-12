<?php

namespace App\Events;

use App\Models\ClxMessage;
use App\Models\VatsimAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClxIssuedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public VatsimAccount $account, public ClxMessage $clxMessage)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clearance.'.$this->account->id),
        ];
    }

    public function broadcastWith(): array
    {
        return $this->clxMessage->toMessageHistoryFormat();
    }

    public function broadcastAs(): string
    {
        return 'clx.issued';
    }
}
