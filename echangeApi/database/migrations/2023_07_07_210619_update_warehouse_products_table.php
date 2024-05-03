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
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->string('sku', 255)->nullable(true);
            $table->float('selling_price');
            $table->float('buying_price');

            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->float('price');
            $table->dropColumn('selling_price');
            $table->dropColumn('buying_price');
            $table->dropColumn('sku');

            $table->dropForeign('warehouse_products_supplier_id_foreign');
            $table->dropColumn('supplier_id');
        });
    }
};
