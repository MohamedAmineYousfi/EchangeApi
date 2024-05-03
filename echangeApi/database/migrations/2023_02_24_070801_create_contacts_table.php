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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('title', 100)->nullable(false);
            $table->string('firstname', 255)->nullable(false);
            $table->string('lastname', 255)->nullable(false);
            $table->string('email', 255)->nullable(true);
            $table->string('phone', 255)->nullable(true);
            $table->string('country', 255)->nullable(true);
            $table->string('state', 255)->nullable(true);
            $table->string('city', 255)->nullable(true);
            $table->string('zipcode', 255)->nullable(true);
            $table->string('address', 255)->nullable(true);
            $table->date('birthday', 255)->nullable(true);
            $table->text('excerpt')->nullable(true);

            $table->morphs('contactable');
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
        Schema::dropIfExists('contacts');
    }
};
