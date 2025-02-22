<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->string('cto_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('cto_time');
        });
    }
};
