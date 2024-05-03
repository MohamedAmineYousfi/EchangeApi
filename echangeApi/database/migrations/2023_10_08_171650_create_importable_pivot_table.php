<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importables', function (Blueprint $table) {
            $table->bigInteger('import_id')->unsigned()->nullable(false);
            $table->foreign('import_id')->references('id')->on('imports');
            $table->morphs('importable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importables');
    }
};
