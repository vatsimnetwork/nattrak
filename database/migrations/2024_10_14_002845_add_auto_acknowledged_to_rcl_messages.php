<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->boolean('is_acknowledged')->default(false);
            $table->dateTime('acknowledged_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropColumn('is_acknowledged');
            $table->dropColumn('acknowledged_at');
        });
    }
};
