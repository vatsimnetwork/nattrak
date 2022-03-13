<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClxMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('clx_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vatsim_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rcl_message_id')->constrained()->cascadeOnDelete();
            $table->string('flight_level');
            $table->string('mach');
            $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete();
            $table->longText('random_routeing')->nullable();
            $table->string('entry_fix');
            $table->string('entry_time_restriction')->nullable();
            $table->text('free_text')->nullable();
            $table->timestamps();
        });

        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->foreignId('clx_message_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('clx_message_id');
        });

        Schema::dropIfExists('clx_messages');
    }
}
