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
        Schema::create('reseller_payments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->dateTime('date')->nullable(false);
            $table->string('source', 255)->default('UNKNOWN');
            $table->string('status', 255)->default('DRAFT');
            $table->float('amount')->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->string('transaction_id', 255)->nullable(true);
            $table->text('transaction_data')->nullable(true);

            $table->bigInteger('reseller_invoice_id')->unsigned()->nullable(false);
            $table->bigInteger('reseller_id')->unsigned()->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reseller_invoice_id')->references('id')->on('reseller_invoices');
            $table->foreign('reseller_id')->references('id')->on('resellers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reseller_payments');
    }
};
