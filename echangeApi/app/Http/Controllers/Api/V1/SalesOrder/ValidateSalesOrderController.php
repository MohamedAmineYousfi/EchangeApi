<?php

namespace App\Http\Controllers\Api\V1\SalesOrder;

use App\Http\Requests\Api\V1\SalesOrder\ValidateSalesOrderRequest;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Support\Interfaces\SalesOrderable;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ValidateSalesOrderController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidateSalesOrderRequest $request, SalesOrder $order)
    {
        if ($order->status == SalesOrder::STATUS_DRAFT) {
            $order->status = SalesOrder::STATUS_VALIDATED;
            $order->save();

            foreach ($order->items as $item) {
                /** @var SalesOrderItem $item */
                $orderable = $item->getOrderable();
                if ($orderable instanceof SalesOrderable) {
                    $orderable->handleSalesOrderValidated($item);
                }
            }
        }

        return $this->reply()->content($order);
    }
}
