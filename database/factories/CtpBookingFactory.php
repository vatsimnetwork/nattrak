<?php

namespace Database\Factories;

use App\Models\CtpBooking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CtpBookingFactory extends Factory
{
    protected $model = CtpBooking::class;

    public function definition(): array
    {
        return [
            'cid' => (string)$this->faker->randomNumber(7),
            'flight_level' => $this->faker->randomElement(['300', '310', '320', '330', '340']),
            'selcal' => strtoupper($this->faker->word()),
            'destination' => $this->faker->randomElement(['EGLL', 'EGCC']),
            'track' => $this->faker->randomLetter(),
            'random_routeing' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
