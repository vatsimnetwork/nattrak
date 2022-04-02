<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockToRclMessages extends Migration
{
    public function up()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->boolean('edit_lock')->default(false);
            $table->dateTime('edit_lock_time')->nullable();
            $table->foreignId('edit_lock_vatsim_account_id')->nullable()->constrained('vatsim_accounts')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rcl_messages', function (Blueprint $table) {
            $table->dropColumn('edit_lock');
            $table->dropColumn('edit_lock_time');
            $table->dropConstrainedForeignId('edit_lock_vatsim_account_id');
        });
    }
}
