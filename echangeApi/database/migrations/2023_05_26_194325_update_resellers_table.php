<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->string('config_manager_app_name', 255)->nullable(true);
            $table->string('config_manager_app_logo', 255)->nullable(true);
            $table->string('config_manager_url_regex', 255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn('config_manager_app_name');
            $table->dropColumn('config_manager_app_logo');
            $table->dropColumn('config_manager_url_regex');
        });
    }
};
