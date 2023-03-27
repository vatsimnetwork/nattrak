<?php

namespace App\Console;

use App\Console\Commands\ClearLockedRclMessagesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Clear RCL message locks
         */
        $schedule->command(ClearLockedRclMessagesCommand::class, ['--minutes' => 5])->everyFiveMinutes();

        /**
         * Update NAT track data from API if enabled
         */
        if (config('services.tracks.auto_update')) {
            $schedule->command('tracks:populate')->at('22:30');
            $schedule->command('tracks:populate')->at('14:30');
        }

        /**
         * Prune CLX and RLX messages after 24 hours
         */
        if (config('services.pruning.prune_msgs')) {
            $schedule->command('model:prune')->hourly();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
