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
        Schema::create('reseller_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->float('unit_price')->nullable(false);
            $table->integer('quantity')->nullable(false);
            $table->float('discount')->nullable(false)->default(0);
            $table->json('taxes')->nullable(false);
            $table->bigInteger('reseller_invoice_id')->unsigned()->nullable(false);

            $table->nullableMorphs('reseller_invoiceable', 'reseller_invoiceable');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reseller_invoice_id')->references('id')->on('reseller_invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reseller_invoice_items');
    }
};
