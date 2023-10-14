<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->string('previous_entry_time')->nullable();
            $table->boolean('new_entry_time')->default(false);
            $table->json('previous_clx_message')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropColumn('previous_entry_time');
            $table->dropColumn('new_entry_time');
            $table->dropColumn('previous_clx_message');
        });
    }
};
