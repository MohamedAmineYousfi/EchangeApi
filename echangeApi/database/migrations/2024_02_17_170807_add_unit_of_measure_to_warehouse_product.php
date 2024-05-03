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
            $table->bigInteger('unit_of_measure_unit_id')->unsigned()->nullable(true);
            $table->foreign('unit_of_measure_unit_id')->references('id')->on('unit_of_measure_units');
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
            $table->dropForeign(['unit_of_measure_unit_id']);
            $table->dropColumn('unit_of_measure_unit_id');
        });
    }
};
