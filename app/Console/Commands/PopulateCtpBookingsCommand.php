<?php

namespace App\Console\Commands;

use App\Models\CtpBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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

        $request = Http::timeout(10)->get(config('services.vatsim.ctp.bookings_endpoint'));
        if (! $request->successful()) {
            $this->error('VATSIM CTP bookings endpoint is not reachable');
            return;
        }
        $bookingsData = json_decode($request->body());

        $bar = $this->output->createProgressBar(count($bookingsData->data));
        $bar->start();

        foreach ($bookingsData->data as $booking) {
            CtpBooking::updateOrCreate(['id' => $booking->id], [
                'cid' => $booking->user_id,
                'destination' => $booking->arr_id,
                'flight_level' => $booking->level,
                'route' => $booking->route,
                'track'  => $booking->track_id,
                'selcal' =>  $booking->selcal
            ]);
            $bar->advance();
        }

        $bar->finish();

        $this->info("Imported bookings");
    }
}
