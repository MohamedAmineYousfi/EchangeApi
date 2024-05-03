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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique()->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('excerpt')->nullable(true);
            $table->float('price')->nullable(false);
            $table->json('taxes')->nullable(false);
            $table->string('picture')->nullable(true);
            $table->string('frequency')->default('1 day')->nullable(false);
            $table->integer('maximum_users')->default(1)->nullable(false);
            $table->json('gallery')->nullable(true);

            $table->bigInteger('reseller_id')->unsigned()->nullable(false);
            $table->bigInteger('default_role_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reseller_id')->references('id')->on('resellers');
            $table->foreign('default_role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
