<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->json('flight_levels')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn('flight_levels');
        });
    }
};
