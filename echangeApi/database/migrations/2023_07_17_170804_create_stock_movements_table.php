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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable(false)->unique();
            $table->enum('status', [StockMovement::STATUS_DRAFT, StockMovement::STATUS_VALIDATED, StockMovement::STATUS_CANCELED])->nullable(false)->default(StockMovement::STATUS_DRAFT);
            $table->enum('movement_type', [StockMovement::TYPE_ADD, StockMovement::TYPE_REMOVE, StockMovement::TYPE_MOVE, StockMovement::TYPE_CORRECT])->nullable(false);
            $table->bigInteger('source_warehouse_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_warehouse_id')->unsigned()->nullable(true);
            $table->text('excerpt')->nullable(true);

            $table->nullableMorphs('trigger_object');
            $table->bigInteger('organization_id')->unsigned()->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('source_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses');
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
        Schema::dropIfExists('stock_movements');
    }
};
