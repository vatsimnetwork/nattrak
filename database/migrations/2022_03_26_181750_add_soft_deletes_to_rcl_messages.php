<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToRclMessages extends Migration
{
    public function up()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
