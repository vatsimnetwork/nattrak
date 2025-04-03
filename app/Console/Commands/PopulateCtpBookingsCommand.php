<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PopulateCtpBookingsCommand extends Command
{
    protected $signature = 'ctp-bookings:populate';

    protected $description = 'Download CTP bookings data from the CTP website';

    public function handle(): void
    {
        if (config('services.vatsim.ctp.bookings_endpoint') == null) {
            $this->error('VATSIM CTP bookings endpoint is not configured');
            return;
        }
    }
}
