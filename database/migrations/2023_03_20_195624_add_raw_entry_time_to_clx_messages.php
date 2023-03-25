<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->string('raw_entry_time_restriction');
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('raw_entry_time_restriction');
        });
    }
};
