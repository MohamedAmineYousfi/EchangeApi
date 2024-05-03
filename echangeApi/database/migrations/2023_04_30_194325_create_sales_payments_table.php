<?php

use App\Models\SalesPayment;
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
        Schema::create('sales_payments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->dateTime('date')->nullable(false);
            $table->string('source', 255)->default(SalesPayment::SOURCE_UNKNOWN);
            $table->string('status', 255)->default(SalesPayment::STATUS_DRAFT);
            $table->float('amount')->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->string('transaction_id', 255)->nullable(true);
            $table->text('transaction_data')->nullable(true);

            $table->bigInteger('sales_invoice_id')->unsigned()->nullable(false);
            $table->bigInteger('organization_id')->unsigned()->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices');
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
        Schema::dropIfExists('sales_payments');
    }
};
