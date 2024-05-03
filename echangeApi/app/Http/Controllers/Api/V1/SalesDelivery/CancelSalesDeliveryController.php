<?php

namespace App\Http\Controllers\Api\V1\SalesDelivery;

use App\Http\Requests\Api\V1\SalesDelivery\CancelSalesDeliveryRequest;
use App\Models\SalesDelivery;
use App\Models\SalesDeliveryItem;
use App\Support\Interfaces\SalesDeliverable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelSalesDeliveryController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelSalesDeliveryRequest $request, SalesDelivery $delivery)
    {
        if ($delivery->status === SalesDelivery::STATUS_VALIDATED) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'DELIVERY_ALREADY_VALIDATED',
                    'detail' => __(
                        'errors.this_delivery_is_already_validated',
                    ),
                    'status' => '400',
                ]),
            ]);
        }

        if ($delivery->status !== SalesDelivery::STATUS_CANCELED) {
            $delivery->status = SalesDelivery::STATUS_CANCELED;
            $delivery->save();

            foreach ($delivery->items as $item) {
                /** @var SalesDeliveryItem $item */
                $deliverable = $item->getDeliverable();
                if ($deliverable instanceof SalesDeliverable) {
                    $deliverable->handleSalesDeliveryCanceled($item);
                }
            }
        }

        return $this->reply()->content($delivery);
    }
}
