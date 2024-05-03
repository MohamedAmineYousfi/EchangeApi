<?php

namespace App\Http\Controllers\Api\V1\PurchasesOrder;

use App\Http\Requests\Api\V1\PurchasesOrder\ValidatePurchasesOrderRequest;
use App\Models\PurchasesOrder;
use App\Models\PurchasesOrderItem;
use App\Support\Interfaces\PurchasesOrderable;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ValidatePurchasesOrderController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidatePurchasesOrderRequest $request, PurchasesOrder $order)
    {
        if ($order->status == PurchasesOrder::STATUS_DRAFT) {
            $order->status = PurchasesOrder::STATUS_VALIDATED;
            $order->save();

            foreach ($order->items as $item) {
                /** @var PurchasesOrderItem $item */
                $orderable = $item->getOrderable();
                if ($orderable instanceof PurchasesOrderable) {
                    $orderable->handlePurchasesOrderValidated($item);
                }
            }
        }

        return $this->reply()->content($order);
    }
}
