<?php

namespace Database\Seeders;

use App\Models\CtpBooking;
use Illuminate\Database\Seeder;

class CtpBookingSeeder extends Seeder
{
    public function run(): void
    {
        CtpBooking::factory()->count(10)->create();
    }
}
