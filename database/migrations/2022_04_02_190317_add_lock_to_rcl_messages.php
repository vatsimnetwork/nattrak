<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockToRclMessages extends Migration
{
    public function up()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            //
        });
    }

    public function down()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            //
        });
    }
}
