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
        Schema::create('tax_groups', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->nullable(false)->default(true);
            $table->string('name', 255)->nullable(false);
            $table->string('country_code', 255)->nullable(false);
            $table->bigInteger('organization_id')->unsigned()->nullable(false);
            $table->text('excerpt')->nullable(true);

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
        Schema::dropIfExists('tax_groups');
    }
};
