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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->boolean('locked')->default(false);
            $table->boolean('allow_negative_inventory')->default(false);
            $table->boolean('allow_unregistered_products')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropIfExists('locked');
            $table->dropIfExists('allow_negative_inventory')->default(false);
            $table->dropIfExists('allow_unregistered_products')->default(false);
        });
    }
};
