<?php

namespace App\Http\Controllers\Api\V1\PurchasesOrder;

use App\Http\Requests\Api\V1\PurchasesOrder\GenerateInvoiceRequest;
use App\Models\PurchasesDelivery;
use App\Models\PurchasesDeliveryItem;
use App\Models\PurchasesOrder;
use Carbon\Carbon;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class GenerateDeliveryController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function generate(GenerateInvoiceRequest $request, PurchasesOrder $order)
    {
        $resolver = json_api()->getDefaultResolver();
        $remainingItems = $order->getDeliveryItemsState()['remainingItems'];

        $deliveryItems = $request->input();
        foreach ($deliveryItems as $key => $item) {
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
                        'detail' => __(
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

        $delivery = new PurchasesDelivery();
        $delivery->status = PurchasesDelivery::STATUS_DRAFT;
        $delivery->expiration_time = Carbon::now()->addDays(30);
        $delivery->organization()->associate($order->organization);
        $delivery->purchasesOrder()->associate($order);
        $delivery->issuer()->associate($order->issuer);
        $delivery->destinationWarehouse()->associate($order->destinationWarehouse);
        $delivery->save();
        $delivery->allowedLocations()->sync($order->allowedLocations);

        foreach ($deliveryItems as $key => $item) {
            $itemData = $remainingItems[$key];
            /** @phpstan-ignore-next-line */
            $purchaseDeliverable = $resolver->getType($itemData['item_type'])::find($key);

            $purchasesDeliveryItem = new PurchasesDeliveryItem();
            $purchasesDeliveryItem->code = $itemData['code'];
            $purchasesDeliveryItem->quantity = $item['quantity'];
            $purchasesDeliveryItem->expected_quantity = $item['quantity'];
            $purchasesDeliveryItem->purchasesDeliverable()->associate($purchaseDeliverable);
            $purchasesDeliveryItem->purchasesDelivery()->associate($delivery);
            $purchasesDeliveryItem->save();
        }

        $order->setOrderStatus();

        return $this->reply()->content($delivery);
    }
}
