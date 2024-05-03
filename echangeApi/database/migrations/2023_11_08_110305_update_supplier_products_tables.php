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
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->renameColumn('price', 'selling_price');
            $table->renameColumn('taxes', 'selling_taxes');
            $table->float('buying_price')->nullable();
            $table->json('buying_taxes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->renameColumn('selling_price', 'price');
            $table->renameColumn('selling_taxes', 'taxes');
            $table->dropColumn('buying_price');
            $table->dropColumn('buying_taxes');
        });
    }
};
