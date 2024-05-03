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
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('taxes');
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('taxes');
        });
        Schema::table('reseller_invoice_items', function (Blueprint $table) {
            $table->dropColumn('taxes');
        });
        Schema::table('reseller_products', function (Blueprint $table) {
            $table->dropColumn('taxes');
        });
        Schema::table('reseller_services', function (Blueprint $table) {
            $table->dropColumn('taxes');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('selling_taxes');
            $table->dropColumn('buying_taxes');
        });
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->dropColumn('selling_taxes');
            $table->dropColumn('buying_taxes');
        });
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('taxes');
        });
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->dropColumn('selling_taxes');
            $table->dropColumn('buying_taxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->json('taxes')->nullable(true);
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->json('taxes')->nullable(true);
        });
        Schema::table('reseller_invoice_items', function (Blueprint $table) {
            $table->json('taxes')->nullable(true);
        });
        Schema::table('reseller_products', function (Blueprint $table) {
            $table->json('taxes')->nullable(true);
        });
        Schema::table('reseller_services', function (Blueprint $table) {
            $table->json('taxes')->nullable(true);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->json('selling_taxes')->nullable();
            $table->json('buying_taxes')->nullable();
        });
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->json('selling_taxes')->nullable();
            $table->json('buying_taxes')->nullable();
        });
        Schema::table('warehouses', function (Blueprint $table) {
            $table->json('taxes')->nullable(true);
        });
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->json('selling_taxes')->nullable();
            $table->json('buying_taxes')->nullable();
        });
    }
};
