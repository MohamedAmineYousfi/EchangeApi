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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id');
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
            $table->string('name');
            $table->string('status');
            $table->timestamp('sold_at')->nullable();
            $table->decimal('sold_amount', 10, 2)->nullable();
            $table->string('registration_number');
            $table->string('batch_numbers');
            $table->string('cadastre');
            $table->string('property_type');
            $table->decimal('taxes_due', 10, 2);
            $table->string('country', 255)->nullable(true);
            $table->string('state', 255)->nullable(true);
            $table->string('city', 255)->nullable(true);
            $table->string('zipcode', 255)->nullable(true);
            $table->string('address', 255)->nullable(true);
            $table->text('excerpt')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('properties');
    }
};
