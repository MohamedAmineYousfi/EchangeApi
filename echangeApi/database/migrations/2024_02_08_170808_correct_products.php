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
            $table->boolean('custom_pricing')->nullable(false)->default(false);
            $table->boolean('custom_taxation')->nullable(false)->default(false);
        });
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->boolean('custom_pricing')->nullable(false)->default(false);
            $table->boolean('custom_taxation')->nullable(false)->default(false);
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
            $table->dropColumn('custom_pricing');
            $table->dropColumn('custom_taxation');
        });
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->dropColumn('custom_pricing');
            $table->dropColumn('custom_taxation');
        });
    }
};
