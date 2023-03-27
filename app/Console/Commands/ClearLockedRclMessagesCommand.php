<?php

namespace App\Console\Commands;

use App\Models\RclMessage;
use Illuminate\Console\Command;

class ClearLockedRclMessagesCommand extends Command
{
    protected $signature = 'rcl-messages:clearlocks {--minutes : Specify minutes locked}';

    protected $description = 'Command description';

    public function handle()
    {
        $this->option('minutes') != null ? $minutes = $this->option('minutes') : $minutes = 5;

        $messagesToUnlock = RclMessage::whereEditLock(true)->get();

        foreach ($messagesToUnlock as $message) {
            if (now()->greaterThan($message->edit_lock_time->addMinutes($minutes))) {
                $message->edit_lock_time = null;
                $message->edit_lock_vatsim_account_id = null;
                $message->edit_lock = false;
                $message->save();
                $this->info('Unlocked RCL message ' . $message->id);
            }
        }
    }
}
