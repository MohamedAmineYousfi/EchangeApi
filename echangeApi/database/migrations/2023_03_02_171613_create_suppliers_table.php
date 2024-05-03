<?php

use App\Constants\BillingInformations;
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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('company_name')->nullable(false);
            $table->string('fiscal_number')->nullable(false);

            $table->string('email', 255)->nullable(false);
            $table->string('phone', 255)->nullable(false);
            $table->string('country', 255)->nullable(false);
            $table->string('state', 255)->nullable(false);
            $table->string('city', 255)->nullable(false);
            $table->string('zipcode', 255)->nullable(false);
            $table->string('address', 255)->nullable(false);

            // billing infos
            $table->string('billing_entity_type', 255)->default(BillingInformations::TYPE_INDIVIDUAL);
            $table->string('billing_company_name')->nullable(true);
            $table->string('billing_firstname', 255)->nullable(true);
            $table->string('billing_lastname', 255)->nullable(true);
            $table->string('billing_country', 255)->nullable(true);
            $table->string('billing_state', 255)->nullable(true);
            $table->string('billing_city', 255)->nullable(true);
            $table->string('billing_zipcode', 255)->nullable(true);
            $table->string('billing_address', 255)->nullable(true);
            $table->string('billing_email', 255)->nullable(true);

            $table->bigInteger('organization_id')->unsigned()->nullable(false);
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
        Schema::dropIfExists('suppliers');
    }
};
