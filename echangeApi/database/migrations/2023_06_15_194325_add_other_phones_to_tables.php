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
            $table->json('phone_extension')->nullable(true);
            $table->json('other_phones')->nullable(true);
        });
        Schema::table('resellers', function (Blueprint $table) {
            $table->json('phone_extension')->nullable(true);
            $table->json('other_phones')->nullable(true);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->json('phone_extension')->nullable(true);
            $table->json('other_phones')->nullable(true);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->json('phone_extension')->nullable(true);
            $table->json('other_phones')->nullable(true);
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->json('phone_extension')->nullable(true);
            $table->json('other_phones')->nullable(true);
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->json('phone_extension')->nullable(true);
            $table->json('other_phones')->nullable(true);
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
            $table->dropColumn('phone_extension');
            $table->dropColumn('other_phones');
        });
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn('phone_extension');
            $table->dropcolumn('other_phones');
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('phone_extension');
            $table->dropcolumn('other_phones');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('phone_extension');
            $table->dropcolumn('other_phones');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('phone_extension');
            $table->dropcolumn('other_phones');
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('phone_extension');
            $table->dropcolumn('other_phones');
        });
    }
};
