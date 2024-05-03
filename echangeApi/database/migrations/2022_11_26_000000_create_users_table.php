<?php

use App\Constants\BillingInformations;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active')->default(false);
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phone')->nullable(true);
            $table->string('locale')->max(2)->min(2);
            $table->timestamp('email_verified_at')->nullable(true);
            $table->string('password');
            $table->boolean('is_staff')->default(false);
            $table->string('profile_image')->nullable();

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

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
