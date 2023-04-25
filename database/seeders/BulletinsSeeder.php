<?php

namespace Database\Seeders;

use App\Models\Bulletin;
use Illuminate\Database\Seeder;

class BulletinsSeeder extends Seeder
{
    public function run(): void
    {
        Bulletin::factory()->count(10)->create();
    }
}
