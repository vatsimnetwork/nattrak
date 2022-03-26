<?php

namespace Database\Factories;

use App\Models\RclMessage;
use App\Models\Track;
use App\Models\VatsimAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RclMessageFactory extends Factory
{
    protected $model = RclMessage::class;

    public function definition(): array
    {
        $callsigns = ['BAW', 'AAL', 'UAL', 'EIN', 'DLH', 'BNY'];
        $destinations = ['KJFK', 'EGLL', 'EDDF', 'KEWR', 'EIDW', 'EDDF', 'EGPH'];
        $flightLevels = ['300', '320', '340'];

        return [
            'callsign' => $callsigns[array_rand($callsigns)] . rand(100, 999),
            'destination' => $destinations[array_rand($destinations)],
            'flight_level' => $flightLevels[array_rand($flightLevels)],
            'mach' => '084',
            'entry_time' => Carbon::now()->format('Hi'),
            'tmi' => '083',
            'request_time' => Carbon::now(),
            'free_text' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'max_flight_level' => '410',
            'vatsim_account_id' => VatsimAccount::first()->id
        ];
    }

    /**
     * Indicate the RCL message is requesting a track.
     *
     * @return Factory
     */
    public function onTrack(): Factory
    {
        $track = Track::active()->first();

        return $this->state(function (array $attributes) use ($track) {
            return [
                'track_id' => $track->id,
                'entry_fix' => strtok($track->last_routeing, " "),
                'random_routeing' => null
            ];
        });
    }

    /**
     * Indicate the RCL message is requesting a random routeing.
     *
     * @return Factory
     **/
    public function onRandomRouteing(): Factory
    {
        return $this->state(function (array $attributes) {
            $routeing = strtoupper($this->faker->text);
            return [
                'track_id' => null,
                'random_routeing' => $routeing,
                'entry_fix' => strtok($routeing, " ")
            ];
        });
    }
}
