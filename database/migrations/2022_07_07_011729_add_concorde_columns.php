<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->string('upper_flight_level')->nullable();
            $table->boolean('is_concorde')->default(false);
        });

        Schema::table('clx_messages', function (Blueprint $table) {
            $table->string('upper_flight_level')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropColumn('upper_flight_level');
            $table->dropColumn('is_concorde');
        });

        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('upper_flight_level');
        });
    }
};
