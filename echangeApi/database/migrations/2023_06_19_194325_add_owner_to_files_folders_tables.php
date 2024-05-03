<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->bigInteger('owner_id')->unsigned()->nullable(true);
            $table->foreign('owner_id')->references('id')->on('users');
        });
        Schema::table('folders', function (Blueprint $table) {
            $table->bigInteger('owner_id')->unsigned()->nullable(true);
            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropIfExists('owner_id');
        });
        Schema::table('folders', function (Blueprint $table) {
            $table->dropIfExists('owner_id');
        });
    }
};
