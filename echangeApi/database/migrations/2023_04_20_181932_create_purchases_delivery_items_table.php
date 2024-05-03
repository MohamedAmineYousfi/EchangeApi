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
        Schema::create('purchases_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false);
            $table->integer('quantity')->nullable(false);
            $table->bigInteger('purchases_delivery_id')->unsigned()->nullable(false);

            $table->nullableMorphs('purchases_deliverable', 'purchases_deliverable');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchases_delivery_id')->references('id')->on('purchases_deliveries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases_delivery_items');
    }
};
