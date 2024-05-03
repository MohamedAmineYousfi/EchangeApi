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
        Schema::create('taxes_tax_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_id');
            $table->unsignedBigInteger('tax_group_id');
            $table->unsignedInteger('seq_number')->default(0);
            $table->timestamps();

            $table->unique(['tax_id', 'tax_group_id']);

            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes_tax_groups');
    }
};
