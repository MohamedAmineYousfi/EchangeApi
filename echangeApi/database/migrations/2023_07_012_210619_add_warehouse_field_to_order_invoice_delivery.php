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
        Schema::table('purchases_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_warehouse_id')->nullable(true);
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses');
        });
        Schema::table('purchases_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_warehouse_id')->nullable(true);
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses');
        });
        Schema::table('purchases_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_warehouse_id')->nullable(true);
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses');
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('source_warehouse_id')->nullable(true);
            $table->foreign('source_warehouse_id')->references('id')->on('warehouses');
        });
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('source_warehouse_id')->nullable(true);
            $table->foreign('source_warehouse_id')->references('id')->on('warehouses');
        });
        Schema::table('sales_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('source_warehouse_id')->nullable(true);
            $table->foreign('source_warehouse_id')->references('id')->on('warehouses');
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
