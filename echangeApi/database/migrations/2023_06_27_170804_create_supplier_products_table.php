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
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 255)->nullable(true);
            $table->string('excerpt')->nullable(true);
            $table->float('price')->nullable(false);
            $table->json('taxes')->nullable(false);

            $table->bigInteger('product_id')->unsigned()->nullable(true);
            $table->bigInteger('supplier_id')->unsigned()->nullable(true);
            $table->bigInteger('organization_id')->unsigned()->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_products');
    }
};
