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
        Schema::create('unit_of_measures', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable(false);

            $table->bigInteger('organization_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations');
        });

        Schema::create('unit_of_measure_units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable(false);
            $table->string('unit_type', 255)->nullable(false);
            $table->float('ratio')->nullable(false);

            $table->bigInteger('organization_id')->unsigned()->nullable(false);
            $table->bigInteger('unit_of_measure_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('unit_of_measure_id')->references('id')->on('unit_of_measures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_of_measure_units');
        Schema::dropIfExists('unit_of_measures');
    }
};
