<?php

namespace App\Console\Commands;

use App\Enums\DatalinkAuthorities;
use App\Enums\RclResponsesEnum;
use App\Models\ClxMessage;
use App\Models\DatalinkAuthority;
use App\Models\RclMessage;
use App\Services\ClxMessageService;
use App\Services\CpdlcService;
use Illuminate\Console\Command;

class AutoAcknowledgeClearCommand extends Command
{
    protected $signature = 'rcl-messages:clear-auto-acknowledged';

    protected $description = 'Move auto acknowledged RCL messages from pending list to processed list';

    public function handle(): void
    {
        $clxMessageService = new ClxMessageService();
        $rclMessages = RclMessage::pending()->where('is_acknowledged', false)->get();
        foreach ($rclMessages as $rclMessage) {
            $clxMessageService->moveAutoAcknowledgedRclToProcessedList($rclMessage);
        }
    }
}
