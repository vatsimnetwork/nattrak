<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->boolean('overwritten')->default(false);
            $table->foreignId('overwritten_by_clx_message_id')->nullable()->constrained('clx_messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('overwritten_by_clx_message_id');
            $table->dropColumn('overwritten');
        });
    }
};
