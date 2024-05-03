<?php

namespace App\Http\Controllers\Api\V1\StockMovement;

use App\Helpers\StockMovement as HelpersStockMovement;
use App\Http\Requests\Api\V1\StockMovement\CancelStockMovementRequest;
use App\Models\StockMovement;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelStockMovementController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelStockMovementRequest $request, StockMovement $stockMovement)
    {
        if ($stockMovement->status === StockMovement::STATUS_VALIDATED) {
            HelpersStockMovement::revertStockMovement($stockMovement);

            $stockMovement->status = StockMovement::STATUS_CANCELED;
            $stockMovement->save();
        } else {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'INVALID_STOCK_MOVEMENT_STATUS',
                    'detail' => __(
                        'errors.stock_movement_should_be_draft',
                    ),
                    'status' => '400',
                ]),
            ]);
        }

        return $this->reply()->content($stockMovement);
    }
}
