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
        Schema::create('purchases_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->float('unit_price')->nullable(false);
            $table->integer('quantity')->nullable(false);
            $table->float('discount')->nullable(false)->default(0);
            $table->json('taxes')->nullable(false);
            $table->bigInteger('purchases_order_id')->unsigned()->nullable(false);

            $table->nullableMorphs('purchases_orderable', 'purchases_orderable');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchases_order_id')->references('id')->on('purchases_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases_order_items');
    }
};
