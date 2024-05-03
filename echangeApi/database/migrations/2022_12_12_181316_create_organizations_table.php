<?php

use App\Constants\BillingInformations;
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
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable(false);
            $table->string('excerpt')->nullable(true);
            $table->string('email')->nullable(false);
            $table->string('address')->nullable(false);
            $table->string('phone')->nullable(false);
            $table->string('logo')->nullable(true);
            $table->json('taxes')->nullable(false);

            // billing infos
            $table->string('billing_entity_type', 255)->default(BillingInformations::TYPE_COMPANY);
            $table->string('billing_company_name')->nullable(true);
            $table->string('billing_firstname', 255)->nullable(true);
            $table->string('billing_lastname', 255)->nullable(true);
            $table->string('billing_country', 255)->nullable(true);
            $table->string('billing_state', 255)->nullable(true);
            $table->string('billing_city', 255)->nullable(true);
            $table->string('billing_zipcode', 255)->nullable(true);
            $table->string('billing_address', 255)->nullable(true);
            $table->string('billing_email', 255)->nullable(true);

            $table->bigInteger('owner_id')->unsigned()->nullable(false);
            $table->bigInteger('reseller_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users');
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
        Schema::dropIfExists('organizations');
    }
};
