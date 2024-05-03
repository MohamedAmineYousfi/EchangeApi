<?php

namespace App\Http\Controllers\Api\V1\PurchasesDelivery;

use App\Helpers\StockMovement as HelpersStockMovement;
use App\Http\Requests\Api\V1\PurchasesDelivery\ValidatePurchasesDeliveryRequest;
use App\Models\PurchasesDelivery;
use App\Models\PurchasesDeliveryItem;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Support\Interfaces\PurchasesDeliverable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ValidatePurchasesDeliveryController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidatePurchasesDeliveryRequest $request, PurchasesDelivery $delivery)
    {
        if ($delivery->status == PurchasesDelivery::STATUS_DRAFT) {
            $order = $delivery->purchasesOrder;
            if ($order) {
                $remainingItems = $order->getDeliveryItemsState()['remainingItems'];
                $resolver = json_api()->getDefaultResolver();

                /** @var PurchasesDeliveryItem $item */
                foreach ($delivery->items as $item) {
                    $key = $item->purchasesDeliverable->id;
                    if ($remainingItems[$key]) {
                        if ($item['quantity'] > $remainingItems[$key]['quantity']) {
                            /** @phpstan-ignore-next-line */
                            $purchaseDeliverable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                            return $this->reply()->errors([
                                Error::fromArray([
                                    'title' => 'DELIVERY_ITEM_QUANTITY_GREATER_THAN_REMAINING_ORDER',
                                    'detail' => __(
                                        'errors.delivery_item_x_quantity_x_is_greater_than_order_quantity_x',
                                        [
                                            'product' => $purchaseDeliverable->name,
                                            'quantity' => $item['quantity'],
                                            'remainingQuantity' => $remainingItems[$key]['quantity'],
                                        ]
                                    ),
                                    'status' => '400',
                                ]),
                            ]);
                        }
                    } else {
                        /** @phpstan-ignore-next-line */
                        $purchaseDeliverable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                        return $this->reply()->errors([
                            Error::fromArray([
                                'title' => 'DELIVERY_ITEM_NOT_FOUND',
                                'message' => __(
                                    'errors.delivery_item_x_not_found_in_order',
                                    [
                                        'product' => $purchaseDeliverable->name,
                                    ]
                                ),
                                'status' => '400',
                            ]),
                        ]);
                    }
                }
            }

            $delivery->status = PurchasesDelivery::STATUS_VALIDATED;
            $delivery->save();

            $stockMovement = null;
            if ($delivery->destinationWarehouse) {
                $stockMovement = new StockMovement();
                $stockMovement->status = StockMovement::STATUS_DRAFT;
                $stockMovement->movement_type = StockMovement::TYPE_ADD;
                $stockMovement->excerpt = null;
                $stockMovement->organization()->associate($delivery->organization);
                $stockMovement->destinationWarehouse()->associate($delivery->destinationWarehouse);
                $stockMovement->triggerObject()->associate($delivery);
                $stockMovement->save();
            }

            foreach ($delivery->items as $item) {
                /** @var PurchasesDeliveryItem $item */
                $deliverable = $item->getDeliverable();
                if ($deliverable instanceof PurchasesDeliverable) {
                    $deliverable->handlePurchasesDeliveryValidated($item);

                    if ($stockMovement) {
                        if ($item->getDeliverable()->getItem()) {
                            $stockMovementItem = new StockMovementItem();
                            $stockMovementItem->quantity = $item->quantity;
                            $stockMovementItem->stockMovement()->associate($stockMovement);
                            $stockMovementItem->storable()->associate($item->getDeliverable()->getItem());
                            $stockMovementItem->save();
                        }
                    }
                }
            }

            if ($stockMovement) {
                HelpersStockMovement::applyStockMovement($stockMovement);
                $stockMovement->status = StockMovement::STATUS_VALIDATED;
                $stockMovement->save();
            }
        }

        return $this->reply()->content($delivery);
    }
}
