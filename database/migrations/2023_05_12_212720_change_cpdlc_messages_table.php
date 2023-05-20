<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cpdlc_messages', function (Blueprint $table) {
            $table->dropColumn('free_text');
            $table->text('message');
            $table->text('caption')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cpdlc_messages', function (Blueprint $table) {
            $table->dropColumn('message');
            $table->dropColumn('caption');
            $table->text('free_text')->nullable();
        });
    }
};
