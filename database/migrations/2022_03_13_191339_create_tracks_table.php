<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->boolean('active');
            $table->text('last_routeing');
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->dateTime('last_active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
