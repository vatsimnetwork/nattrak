<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatalinkAuthorityToClx extends Migration
{
    public function up()
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->string('datalink_authority', 4)->default('NATX');
        });
    }

    public function down()
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('datalink_authority');
        });
    }
}
