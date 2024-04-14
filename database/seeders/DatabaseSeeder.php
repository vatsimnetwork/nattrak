<?php

namespace Database\Seeders;

use App\Models\VatsimAccount;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        VatsimAccount::updateOrCreate([
            'id' => '9999999',
            'access_level' => 3,
            'given_name' => 'System',
            'surname' => 'User',
            'rating_int' => 9,
        ]);
    }
}
