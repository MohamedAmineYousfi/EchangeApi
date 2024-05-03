<?php

use App\Models\PurchasesOrder;
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
        Schema::create('purchases_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->string('status', 255)->default(PurchasesOrder::STATUS_DRAFT);
            $table->dateTime('expiration_time')->nullable(false);
            $table->json('discounts')->nullable(false);
            $table->text('excerpt')->nullable(true);

            $table->bigInteger('organization_id')->unsigned()->nullable(true);
            $table->nullableMorphs('issuer');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('purchases_orders');
    }
};
