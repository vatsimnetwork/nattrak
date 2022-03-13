<?php

use App\Models\VatsimAccount;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRclMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('rcl_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vatsim_account_id')->constrained()->cascadeOnDelete();
            $table->string('callsign');
            $table->string('destination');
            $table->string('flight_level');
            $table->string('mach');
            $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete();
            $table->longText('random_routeing')->nullable();
            $table->string('entry_fix');
            $table->string('entry_time');
            $table->string('tmi')->nullable();
            $table->dateTime('request_time');
            $table->text('free_text')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rcl_messages');
    }
}
