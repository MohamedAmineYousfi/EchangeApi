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
            $table->timestamp('verification_code_expires_at')->nullable();
            $table->boolean('is_2fa_enabled')->default(false);
            $table->string('two_fa_code')->nullable();
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
            $table->dropColumn('verification_code_expires_at');
            $table->dropColumn('is_2fa_enabled');
            $table->dropColumn('two_fa_code');
        });
    }
};
