<?php

use App\Constants\BillingInformations;
use App\Models\ResellerInvoice;
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
        Schema::create('reseller_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->dateTime('expiration_time')->nullable(false);
            $table->text('excerpt')->nullable(true);
            $table->string('status', 255)->default(ResellerInvoice::STATUS_DRAFT);

            $table->string('billing_entity_type', 255)->default(BillingInformations::TYPE_INDIVIDUAL);
            $table->string('billing_company_name')->nullable(true);
            $table->string('billing_firstname', 255)->nullable(true);
            $table->string('billing_lastname', 255)->nullable(true);
            $table->string('billing_country', 255)->nullable(false);
            $table->string('billing_state', 255)->nullable(false);
            $table->string('billing_city', 255)->nullable(false);
            $table->string('billing_zipcode', 255)->nullable(false);
            $table->string('billing_address', 255)->nullable(false);
            $table->string('billing_email', 255)->nullable(false);
            $table->json('discounts')->nullable(false);

            $table->bigInteger('reseller_id')->unsigned()->nullable(true);

            $table->nullableMorphs('recipient');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('reseller_invoices');
    }
};
