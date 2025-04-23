<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ctp_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('cid')->index();
            $table->string('flight_level')->nullable();
            $table->string('selcal')->nullable();
            $table->string('destination');
            $table->string('track')->nullable();
            $table->string('random_routeing')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctp_bookings');
    }
};
