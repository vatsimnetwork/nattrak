<?php

namespace App\Console\Commands;

use App\Models\Track;
use App\Services\TracksService;
use Exception;
use Illuminate\Console\Command;

class PopulateTracksCommand extends Command
{
    protected $signature = 'tracks:populate';

    protected $description = 'Populate active NAT tracks';

    public function handle(TracksService $tracksService): void
    {
        $this->line('Downloading tracks...');

        try {
            $tracks = $tracksService->getTracks();
            $this->line('Downloaded tracks.');
        } catch (Exception $e) {
            $this->error('Could not download tracks.');

            return;
        }

        $this->line('Deactivating old tracks...');
        foreach (Track::whereActive(true)->get() as $track) {
            $track->deactivate();
        }

        foreach ($tracks as $track) {
            $this->line("Processing track identifier $track[ident]...");
            Track::updateOrCreate(['identifier' => $track['ident']], [
                'active' => true,
                'last_routeing' => $track['route'],
                'valid_from' => $track['valid_from'],
                'valid_to' => $track['valid_to'],
                'last_active' => now(),
                'flight_levels' => $track['flight_levels'],
            ]);
        }

        activity()->log('Updated track data');
        $this->info('Completed!');
    }
}
