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
        Schema::create('stock_movement_items', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->nullable(false);
            $table->bigInteger('stock_movement_id')->unsigned()->nullable(true);

            $table->morphs('storable');
            $table->timestamps();

            $table->foreign('stock_movement_id')->references('id')->on('stock_movements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_movement_items');
    }
};
