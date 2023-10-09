<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->boolean('cancelled')->default(false);
            $table->string('cancellation_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
            $table->dropColumn('cancelled');
        });
    }
};
