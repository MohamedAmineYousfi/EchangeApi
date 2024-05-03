<?php

namespace App\Http\Controllers\Api\V1\PurchasesDelivery;

use App\Http\Requests\Api\V1\PurchasesDelivery\CancelPurchasesDeliveryRequest;
use App\Models\PurchasesDelivery;
use App\Models\PurchasesDeliveryItem;
use App\Support\Interfaces\PurchasesDeliverable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelPurchasesDeliveryController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelPurchasesDeliveryRequest $request, PurchasesDelivery $delivery)
    {
        if ($delivery->status === PurchasesDelivery::STATUS_VALIDATED) {
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

        if ($delivery->status !== PurchasesDelivery::STATUS_CANCELED) {
            $delivery->status = PurchasesDelivery::STATUS_CANCELED;
            $delivery->save();

            foreach ($delivery->items as $item) {
                /** @var PurchasesDeliveryItem $item */
                $deliverable = $item->getDeliverable();
                if ($deliverable instanceof PurchasesDeliverable) {
                    $deliverable->handlePurchasesDeliveryCanceled($item);
                }
            }
        }

        return $this->reply()->content($delivery);
    }
}
