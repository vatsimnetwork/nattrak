<?php

namespace Database\Seeders;

use App\Models\RclMessage;
use Illuminate\Database\Seeder;

class RclMessageSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding RCL');
        RclMessage::factory()->onTrack()->count(1)->create();
        RclMessage::factory()->onRandomRouteing()->count(1)->create();
    }
}
