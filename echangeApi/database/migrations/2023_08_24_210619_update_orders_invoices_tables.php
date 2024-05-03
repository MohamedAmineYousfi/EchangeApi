<?php

use App\Support\Classes\BaseOrder;
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
        Schema::table('purchases_orders', function (Blueprint $table) {
            $table->string('delivery_status')->default(BaseOrder::DELIVERY_STATUS_PENDING);
            $table->string('invoicing_status')->default(BaseOrder::INVOICING_STATUS_PENDING);
            $table->string('invoicing_type')->default(BaseOrder::INVOICING_TYPE_PRODUCT);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('delivery_status')->default(BaseOrder::DELIVERY_STATUS_PENDING);
            $table->string('invoicing_status')->default(BaseOrder::INVOICING_STATUS_PENDING);
            $table->string('invoicing_type')->default(BaseOrder::INVOICING_TYPE_PRODUCT);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
