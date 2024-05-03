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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->nullable(false)->default(true);
            $table->string('name', 255)->nullable(false);
            $table->string('label', 255)->nullable(false);
            $table->string('tax_number', 255)->nullable(true);
            $table->string('tax_type', 255)->nullable(false);
            $table->string('calculation_type', 255)->nullable(false);
            $table->string('calculation_base', 255)->nullable(false);
            $table->float('value')->nullable(false);
            $table->text('excerpt')->nullable(true);

            $table->bigInteger('organization_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};
