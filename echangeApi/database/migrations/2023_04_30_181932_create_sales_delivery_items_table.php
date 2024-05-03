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
        Schema::create('sales_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false);
            $table->integer('quantity')->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->bigInteger('sales_delivery_id')->unsigned()->nullable(false);

            $table->nullableMorphs('sales_deliverable', 'sales_deliverable');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sales_delivery_id')->references('id')->on('sales_deliveries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_delivery_items');
    }
};
