<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxFL extends Migration
{
    public function up()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->string('max_flight_level')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropColumn('max_flight_level');
        });
    }
}
