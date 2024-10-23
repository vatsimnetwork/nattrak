<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->string('datalink_authority_id')->default(null)->change();
            $table->foreign('datalink_authority_id')->references('id')->on('datalink_authorities');
        });

        Schema::table('cpdlc_messages', function (Blueprint $table) {
            $table->string('datalink_authority_id')->default(null)->change();
            $table->foreign('datalink_authority_id')->references('id')->on('datalink_authorities');
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            Schema::table('clx_messages', function (Blueprint $table) {
                $table->dropForeign('clx_messages_datalink_authority_id_foreign');
                $table->string('datalink_authority', 4)->default('NATX')->change();
            });

            Schema::table('cpdlc_messages', function (Blueprint $table) {
                $table->dropForeign('cpdlc_messages_datalink_authority_id_foreign');
                $table->string('datalink_authority', 4)->default(null)->change();
            });
        });
    }
};
