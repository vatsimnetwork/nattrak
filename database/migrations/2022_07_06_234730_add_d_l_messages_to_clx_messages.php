<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->text('simple_datalink_message');
            $table->json('datalink_message');
        });
    }

    public function down()
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('simple_datalink_message');
            $table->dropColumn('datalink_message');
        });
    }
};
