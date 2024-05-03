<?php

namespace App\Http\Controllers\Api\V1\PurchasesOrder;

use App\Http\Requests\Api\V1\PurchasesOrder\CancelPurchasesOrderRequest;
use App\Models\PurchasesDelivery;
use App\Models\PurchasesInvoice;
use App\Models\PurchasesOrder;
use App\Models\PurchasesOrderItem;
use App\Support\Interfaces\PurchasesOrderable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelPurchasesOrderController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelPurchasesOrderRequest $request, PurchasesOrder $order)
    {
        if ($order->deliveries()->where('status', PurchasesDelivery::STATUS_VALIDATED)->count() > 0) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'ORDER_HAS_VALIDATED_DELIVERIES',
                    'detail' => __(
                        'errors.this_order_has_validated_deliveries',
                    ),
                    'status' => '400',
                ]),
            ]);
        }

        if ($order->invoices()->where('status', PurchasesInvoice::STATUS_VALIDATED)->count() > 0) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'ORDER_HAS_VALIDATED_INVOICE',
                    'detail' => __(
                        'errors.this_order_has_validated_invoice',
                    ),
                    'status' => '400',
                ]),
            ]);
        }

        if ($order->status !== PurchasesOrder::STATUS_CANCELED) {
            $order->status = PurchasesOrder::STATUS_CANCELED;
            $order->save();

            foreach ($order->items as $item) {
                /** @var PurchasesOrderItem $item */
                $orderable = $item->getOrderable();
                if ($orderable instanceof PurchasesOrderable) {
                    $orderable->handlePurchasesOrderCanceled($item);
                }
            }
        }

        return $this->reply()->content($order);
    }
}
