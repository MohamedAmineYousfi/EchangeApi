<?php

namespace App\Http\Controllers\Api\V1\StockMovementItem;

use App\Http\Requests\Api\V1\StockMovementItem\BulkUpdateRequest;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class BulkUpdateController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function bulkUpdate(BulkUpdateRequest $request)
    {
        $stockMovement = StockMovement::find($request->stockMovement);

        foreach ($request->items as $item) {
            $stockMovementItem = StockMovementItem::find($item['id']);
            $stockMovementItem->quantity = $item['quantity'];
            $stockMovementItem->save();
        }

        return $this->reply()->content($stockMovement);
    }
}
