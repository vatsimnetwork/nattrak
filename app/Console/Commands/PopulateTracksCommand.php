<?php

namespace App\Console\Commands;

use App\Models\Track;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class PopulateTracksCommand extends Command
{
    protected $signature = 'tracks:populate {--url= : Specify a custom API endpoint to pull from}';

    protected $description = 'Populate active NAT tracks';

    public const TRACK_API_ENDPOINT = 'https://tracks.ganderoceanic.ca/data';

    public function handle()
    {
        $endpoint = self::TRACK_API_ENDPOINT;
        if ($this->option('url')) {
            $endpoint = $this->option('url');
        }

        $this->info('Downloading tracks from API ('.$endpoint.')');
        $trackData = Http::get($endpoint, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if ($trackData) {
            $tracks = json_decode(($trackData));
            $this->line('Tracks decoded.');
        } else {
            $this->error('Could not decode tracks');

            return;
        }

        foreach (Track::whereActive(true)->get() as $track) {
            $track->deactivate();
        }

        foreach ($tracks as $track) {
            $this->line("Processing track identifier {$track->id} ...");
            $routeingString = '';
            foreach ($track->route as $fix) {
                $routeingString .= "$fix->name ";
            }
            Track::updateOrCreate(['identifier' => $track->id], [
                'last_routeing' => trim($routeingString),
                'valid_from' => Carbon::createFromTimestamp($track->validFrom),
                'valid_to' => Carbon::createFromTimestamp($track->validTo),
                'active' => true,
                'last_active' => now(),
                'flight_levels' => $track->flightLevels,
            ]);
        }

        activity()->log('Updated track data');
        $this->info('Completed!');
    }
}
