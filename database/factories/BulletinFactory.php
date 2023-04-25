<?php

namespace Database\Factories;

use App\Models\Bulletin;
use Illuminate\Database\Eloquent\Factories\Factory;

class BulletinFactory extends Factory
{
    protected $model = Bulletin::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'subtitle' => $this->faker->sentence(),
            'content' => $this->faker->realText(),
            'action_url' => $this->faker->url(),
            'alert_controllers' => $this->faker->boolean(),
            'alert_pilots' => $this->faker->boolean(),
        ];
    }
}
