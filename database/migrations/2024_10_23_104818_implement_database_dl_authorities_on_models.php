<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->renameColumn('datalink_authority', 'datalink_authority_id');
        });

        Schema::table('cpdlc_messages', function (Blueprint $table) {
            $table->renameColumn('datalink_authority', 'datalink_authority_id');
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            Schema::table('clx_messages', function (Blueprint $table) {
                $table->renameColumn('datalink_authority_id', 'datalink_authority');
            });

            Schema::table('cpdlc_messages', function (Blueprint $table) {
                $table->renameColumn('datalink_authority_id', 'datalink_authority');
            });
        });
    }
};
