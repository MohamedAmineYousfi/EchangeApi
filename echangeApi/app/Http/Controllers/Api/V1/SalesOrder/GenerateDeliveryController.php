<?php

namespace App\Http\Controllers\Api\V1\SalesOrder;

use App\Http\Requests\Api\V1\SalesOrder\GenerateInvoiceRequest;
use App\Models\SalesDelivery;
use App\Models\SalesDeliveryItem;
use App\Models\SalesOrder;
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
    public function generate(GenerateInvoiceRequest $request, SalesOrder $order)
    {
        $resolver = json_api()->getDefaultResolver();
        $remainingItems = $order->getDeliveryItemsState()['remainingItems'];

        $deliveryItems = $request->input();
        foreach ($deliveryItems as $key => $item) {
            if ($remainingItems[$key]) {
                if ($item['quantity'] > $remainingItems[$key]['quantity']) {
                    /** @phpstan-ignore-next-line */
                    $salesDeliverable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                    return $this->reply()->errors([
                        Error::fromArray([
                            'title' => 'DELIVERY_ITEM_QUANTITY_GREATER_THAN_REMAINING_ORDER',
                            'detail' => __(
                                'errors.delivery_item_x_quantity_x_is_greater_than_order_quantity_x',
                                [
                                    'product' => $salesDeliverable->name,
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
                $salesDeliverable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                return $this->reply()->errors([
                    Error::fromArray([
                        'title' => 'DELIVERY_ITEM_NOT_FOUND',
                        'detail' => __(
                            'errors.delivery_item_x_not_found_in_order',
                            [
                                'product' => $salesDeliverable->name,
                            ]
                        ),
                        'status' => '400',
                    ]),
                ]);
            }
        }

        $delivery = new SalesDelivery();
        $delivery->status = SalesDelivery::STATUS_DRAFT;
        $delivery->expiration_time = Carbon::now()->addDays(30);
        $delivery->delivery_entity_type = $order->billing_entity_type;
        $delivery->delivery_company_name = $order->billing_company_name;
        $delivery->delivery_firstname = $order->billing_firstname;
        $delivery->delivery_lastname = $order->billing_lastname;
        $delivery->delivery_country = $order->billing_country;
        $delivery->delivery_state = $order->billing_state;
        $delivery->delivery_city = $order->billing_city;
        $delivery->delivery_zipcode = $order->billing_zipcode;
        $delivery->delivery_address = $order->billing_address;
        $delivery->delivery_email = $order->billing_email;
        $delivery->organization()->associate($order->organization);
        $delivery->salesOrder()->associate($order);
        $delivery->recipient()->associate($order->recipient);
        $delivery->sourceWarehouse()->associate($order->sourceWarehouse);
        $delivery->save();
        $delivery->allowedLocations()->sync($order->allowedLocations);

        foreach ($deliveryItems as $key => $item) {
            $itemData = $remainingItems[$key];
            /** @phpstan-ignore-next-line */
            $salesDeliverable = $resolver->getType($itemData['item_type'])::find($key);

            $salesDeliveryItem = new SalesDeliveryItem();
            $salesDeliveryItem->code = $itemData['code'];
            $salesDeliveryItem->quantity = $item['quantity'];
            $salesDeliveryItem->expected_quantity = $item['quantity'];
            $salesDeliveryItem->salesDeliverable()->associate($salesDeliverable);
            $salesDeliveryItem->salesDelivery()->associate($delivery);
            $salesDeliveryItem->save();
        }

        return $this->reply()->content($delivery);
    }
}
