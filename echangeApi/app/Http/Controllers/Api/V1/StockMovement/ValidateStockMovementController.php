<?php

namespace App\Http\Controllers\Api\V1\StockMovement;

use App\Helpers\StockMovement as HelpersStockMovement;
use App\Http\Requests\Api\V1\StockMovement\ValidateStockMovementRequest;
use App\Models\StockMovement;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Error as GlobalError;

class ValidateStockMovementController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidateStockMovementRequest $request, StockMovement $stockMovement)
    {
        if ($stockMovement->status === StockMovement::STATUS_DRAFT) {
            try {
                HelpersStockMovement::validateStockMovement($stockMovement);
            } catch (GlobalError $e) {
                return $this->reply()->errors([
                    Error::fromArray([
                        'title' => 'INVALID_STOCK_MOVEMENT_STATUS',
                        'detail' => $e->getMessage(),
                        'status' => '400',
                    ]),
                ]);
            }

            HelpersStockMovement::applyStockMovement($stockMovement);

            $stockMovement->status = StockMovement::STATUS_VALIDATED;
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
