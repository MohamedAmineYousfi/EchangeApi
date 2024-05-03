<?php

use App\Constants\BillingInformations;
use App\Models\SalesDelivery;
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
        Schema::create('sales_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->string('status', 255)->default(SalesDelivery::STATUS_DRAFT);
            $table->dateTime('expiration_time')->nullable(false);
            $table->text('excerpt')->nullable(true);

            $table->string('delivery_entity_type', 255)->default(BillingInformations::TYPE_INDIVIDUAL);
            $table->string('delivery_company_name')->nullable(true);
            $table->string('delivery_firstname', 255)->nullable(true);
            $table->string('delivery_lastname', 255)->nullable(true);
            $table->string('delivery_country', 255)->nullable(false);
            $table->string('delivery_state', 255)->nullable(false);
            $table->string('delivery_city', 255)->nullable(false);
            $table->string('delivery_zipcode', 255)->nullable(false);
            $table->string('delivery_address', 255)->nullable(false);
            $table->string('delivery_email', 255)->nullable(false);

            $table->bigInteger('organization_id')->unsigned()->nullable(true);
            $table->bigInteger('sales_order_id')->unsigned()->nullable(true);
            $table->nullableMorphs('recipient');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_deliveries');
    }
};
