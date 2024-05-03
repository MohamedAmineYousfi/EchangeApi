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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id');
            $table->string('name');
            $table->text('excerpt')->nullable(true);
            $table->string('auction_type');
            $table->string('object_type');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('activated_timer')->default(false);
            $table->dateTime('pre_opening_at')->nullable();
            $table->integer('extension_time')->default(0);
            $table->integer('delay')->default(0);
            $table->json('authorized_payments')->nullable();
            $table->string('country', 255)->nullable(true);
            $table->string('state', 255)->nullable(true);
            $table->string('city', 255)->nullable(true);
            $table->string('zipcode', 255)->nullable(true);
            $table->string('address', 255)->nullable(true);
            $table->decimal('lat', 10, 6)->nullable(true);
            $table->decimal('long', 10, 6)->nullable(true);
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
        Schema::dropIfExists('auctions');
    }
};
