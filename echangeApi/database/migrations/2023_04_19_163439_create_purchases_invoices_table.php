<?php

use App\Models\PurchasesInvoice;
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
        Schema::create('purchases_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->string('status', 255)->default(PurchasesInvoice::STATUS_DRAFT);
            $table->dateTime('expiration_time')->nullable(false);
            $table->json('discounts')->nullable(false);
            $table->text('excerpt')->nullable(true);

            $table->bigInteger('organization_id')->unsigned()->nullable(true);
            $table->bigInteger('purchases_order_id')->unsigned()->nullable(true);
            $table->nullableMorphs('issuer');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('purchases_order_id')->references('id')->on('purchases_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases_invoices');
    }
};
