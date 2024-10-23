<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->string('target_datalink_authority_id')->nullable();
            $table->foreign('target_datalink_authority_id')->references('id')->on('datalink_authorities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropForeign('rcl_messages_target_datalink_authority_id_foreign');
            $table->dropColumn('target_datalink_authority_id');
        });
    }
};
