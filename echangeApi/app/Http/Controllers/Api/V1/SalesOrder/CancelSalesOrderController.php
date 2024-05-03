<?php

namespace App\Http\Controllers\Api\V1\SalesOrder;

use App\Http\Requests\Api\V1\SalesOrder\CancelSalesOrderRequest;
use App\Models\SalesDelivery;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Support\Interfaces\SalesOrderable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelSalesOrderController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelSalesOrderRequest $request, SalesOrder $order)
    {
        if ($order->deliveries()->where('status', SalesDelivery::STATUS_VALIDATED)->count() > 0) {
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

        if ($order->invoices()->where('status', SalesInvoice::STATUS_VALIDATED)->count() > 0) {
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

        if ($order->status !== SalesOrder::STATUS_CANCELED) {
            $order->status = SalesOrder::STATUS_CANCELED;
            $order->save();

            foreach ($order->items as $item) {
                /** @var SalesOrderItem $item */
                $orderable = $item->getOrderable();
                if ($orderable instanceof SalesOrderable) {
                    $orderable->handleSalesOrderCanceled($item);
                }
            }
        }

        return $this->reply()->content($order);
    }
}
