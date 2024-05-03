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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('size', 255)->nullable(false);
            $table->string('path', 255)->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->json('file_history')->nullable(false);

            $table->nullableMorphs('object');
            $table->bigInteger('folder_id')->unsigned()->nullable(true);
            $table->bigInteger('organization_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('folder_id')->references('id')->on('folders');
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
        Schema::dropIfExists('files');
    }
};
