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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_type', 255)->nullable(true);
        });
        Schema::table('resellers', function (Blueprint $table) {
            $table->string('phone_type', 255)->nullable(true);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('phone_type', 255)->nullable(true);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_type', 255)->nullable(true);
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('phone_type', 255)->nullable(true);
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('phone_type', 255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_type');
        });
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn('phone_type');
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('phone_type');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('phone_type');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('phone_type');
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('phone_type');
        });
    }
};
