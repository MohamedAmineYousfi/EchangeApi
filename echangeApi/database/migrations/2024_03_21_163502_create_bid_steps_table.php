<?php

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
        Schema::create('bid_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->decimal('amount_min', 10, 2);
            $table->decimal('amount_max', 10, 2)->nullable();
            $table->decimal('bid_amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bid_steps');
    }
};
