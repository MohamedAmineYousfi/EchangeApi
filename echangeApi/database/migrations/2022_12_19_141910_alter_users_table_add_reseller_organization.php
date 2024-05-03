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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('reseller_id')->unsigned()->nullable(true);
            $table->bigInteger('organization_id')->unsigned()->nullable(true);

            $table->foreign('reseller_id')->references('id')->on('resellers');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('reseller_id');
            $table->dropForeign('organization_id');

            $table->dropColumn('reseller_id');
            $table->dropColumn('organization_id');
        });
    }
};
