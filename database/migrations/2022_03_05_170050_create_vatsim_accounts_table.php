<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVatsimAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('vatsim_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('given_name')->nullable();
            $table->string('surname')->nullable();
            $table->integer('rating_int');
            $table->integer('access_level')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vatsim_accounts');
    }
}
