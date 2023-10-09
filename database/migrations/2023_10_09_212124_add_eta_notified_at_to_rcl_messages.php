<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dateTIme('new_entry_time_notified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropColumn('new_entry_time_notified_at');
        });
    }
};
