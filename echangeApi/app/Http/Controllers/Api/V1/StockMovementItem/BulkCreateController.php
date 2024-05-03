<?php

namespace App\Http\Controllers\Api\V1\StockMovementItem;

use App\Http\Requests\Api\V1\StockMovementItem\BulkCreateRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class BulkCreateController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function bulkCreate(BulkCreateRequest $request)
    {
        $stockMovement = StockMovement::find($request->stockMovement);

        foreach ($request->items as $item) {
            $stockMovementItem = new StockMovementItem();
            $stockMovementItem->stockMovement()->associate($stockMovement);
            $stockMovementItem->storable()->associate(Product::find($item['storable']['id']));
            $stockMovementItem->quantity = $item['quantity'];
            $stockMovementItem->save();
        }

        return $this->reply()->content($stockMovement);
    }
}
