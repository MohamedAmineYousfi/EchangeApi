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
        Schema::table('properties', function (Blueprint $table) {
            $table->date('transaction_date')->nullable();
            $table->text('transaction_excerpt')->nullable();
            $table->json('transactions')->nullable();
            $table->string('customer')->nullable();
            $table->foreignId('payment_received_by')->nullable();

            $table->foreign('payment_received_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['payment_received_by', 'customer', 'transactions', 'transaction_excerpt', 'transaction_date']);
        });
    }
};
