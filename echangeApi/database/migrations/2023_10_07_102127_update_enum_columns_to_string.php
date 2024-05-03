<?php

use App\Models\StockMovement;
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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('status', 255)->nullable(false)->default(StockMovement::STATUS_DRAFT)->change();
            $table->string('movement_type', 255)->nullable(false)->change();
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
