<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpdlcMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('cpdlc_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilot_id')->constrained('vatsim_accounts')->cascadeOnDelete();
            $table->string('pilot_callsign', 7);
            $table->string('datalink_authority', 4);
            $table->text('free_text')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cpdlc_messages');
    }
}
