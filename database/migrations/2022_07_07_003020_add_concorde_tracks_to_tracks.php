<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private $tracks = [
        [
            'identifier' => 'SO',
            'routeing' => 'https://ganderoceanic.ca/pilots/tracks/concorde',
        ],
        [
            'identifier' => 'SN',
            'routeing' => 'https://ganderoceanic.ca/pilots/tracks/concorde',
        ],
        [
            'identifier' => 'SM',
            'routeing' => 'https://ganderoceanic.ca/pilots/tracks/concorde',
        ],
    ];

    public function up()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->boolean('concorde')->default(false);
        });

        foreach ($this->tracks as $track) {
            \App\Models\Track::create([
                'identifier' => $track['identifier'],
                'last_routeing' => $track['routeing'],
                'active' => false,
                'last_active' => \Carbon\Carbon::create(1970),
                'concorde' => true,
                'valid_from' => \Carbon\Carbon::create(1970),
                'valid_to' => \Carbon\Carbon::create(1970),
            ]);
        }
    }

    public function down()
    {
        foreach ($this->tracks as $track) {
            \App\Models\Track::whereIdentifier($track['identifier'])->first()->delete();
        }

        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn('concorde');
        });
    }
};
