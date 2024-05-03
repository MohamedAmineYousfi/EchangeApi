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
        Schema::table('sales_delivery_items', function (Blueprint $table) {
            $table->float('expected_quantity')->default(0)->nullable(false)->change();
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('purchases_delivery_items', function (Blueprint $table) {
            $table->float('expected_quantity')->default(0)->nullable(false)->change();
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('stock_movement_items', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('purchases_invoice_items', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('purchases_order_items', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

        Schema::table('reseller_invoice_items', function (Blueprint $table) {
            $table->float('quantity')->default(0)->nullable(false)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
