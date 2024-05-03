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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique()->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('excerpt')->nullable(true);
            $table->float('price')->nullable(false);
            $table->json('taxes')->nullable(false);
            $table->string('picture')->nullable(true);
            $table->json('gallery')->nullable(true);

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
        Schema::dropIfExists('products');
    }
};
