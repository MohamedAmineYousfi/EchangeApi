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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('taxes_due');
            $table->double('owed_taxes_school_board')->nullable();
            $table->double('owed_taxes_municipality')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->json('taxes_due')->nullable();
            $table->dropColumn('owed_taxes_municipality');
            $table->dropColumn('owed_taxes_school_board');
        });
    }
};
