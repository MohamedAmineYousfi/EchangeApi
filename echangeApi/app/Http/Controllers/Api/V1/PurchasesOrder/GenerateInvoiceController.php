<?php

namespace App\Http\Controllers\Api\V1\PurchasesOrder;

use App\Http\Requests\Api\V1\PurchasesOrder\GenerateInvoiceRequest;
use App\Models\PurchasesInvoice;
use App\Models\PurchasesInvoiceItem;
use App\Models\PurchasesOrder;
use Carbon\Carbon;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class GenerateInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function generate(GenerateInvoiceRequest $request, PurchasesOrder $order)
    {
        if ($order->status != PurchasesOrder::STATUS_VALIDATED) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'ORDER_STATUS_NOT_VALIDATED',
                    'status' => '400',
                ]),
            ]);
        }
        if ($order->invoicing_status == PurchasesOrder::INVOICING_STATUS_INVOICED) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'ORDER_ALREADY_INVOICED',
                    'status' => '400',
                ]),
            ]);
        }

        $payload = $request->input();
        if (count($order->invoices) > 0) {
            if ($order->invoicing_type != $payload['invoicingType']) {
                return $this->reply()->errors([
                    Error::fromArray([
                        'title' => 'CANNOT_CHANGE_ORDER_INVOICING_TYPE',
                        'status' => '400',
                    ]),
                ]);
            }
        }

        if ($payload['invoicingType'] == 'COMPLETE') {
            $invoice = $this->generateOrderCompleteInvoice($order);
            $order->invoicing_type = PurchasesOrder::INVOICING_TYPE_PRODUCT;
            $order->save();
        } elseif ($payload['invoicingType'] == PurchasesOrder::INVOICING_TYPE_PRODUCT) {
            $invoice = $this->generatePartialProductInvoice($order, $payload);
            if (! $invoice instanceof PurchasesInvoice) {
                return $this->reply()->errors([$invoice]);
            }
            $order->invoicing_type = PurchasesOrder::INVOICING_TYPE_PRODUCT;
            $order->save();
        } elseif ($payload['invoicingType'] == PurchasesOrder::INVOICING_TYPE_AMOUNT) {
            $invoice = $this->generatePartialAmountInvoice($order, $payload);
            if (! $invoice instanceof PurchasesInvoice) {
                return $this->reply()->errors([$invoice]);
            }
            $order->invoicing_type = PurchasesOrder::INVOICING_TYPE_AMOUNT;
            $order->save();
        } else {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'INVALID_INVOICING_TYPE',
                    'status' => '400',
                ]),
            ]);
        }

        $order->setOrderStatus();

        return $this->reply()->content($invoice);
    }

    private function generateOrderCompleteInvoice($order)
    {
        $invoice = new PurchasesInvoice();
        $invoice->status = PurchasesInvoice::STATUS_VALIDATED;
        $invoice->invoice_type = PurchasesOrder::INVOICING_TYPE_PRODUCT;
        $invoice->expiration_time = Carbon::now()->addDays(30);
        $invoice->discounts = $order->discounts;
        $invoice->has_no_taxes = $order->has_no_taxes;
        $invoice->organization()->associate($order->organization);
        $invoice->purchasesOrder()->associate($order);
        $invoice->issuer()->associate($order->issuer);
        $invoice->destinationWarehouse()->associate($order->destinationWarehouse);
        $invoice->save();

        $invoice->allowedLocations()->sync($order->allowedLocations);

        foreach ($order->items as $item) {
            $purchasesInvoiceItem = new PurchasesInvoiceItem();
            $purchasesInvoiceItem->code = $item->code;
            $purchasesInvoiceItem->excerpt = $item->excerpt;
            $purchasesInvoiceItem->unit_price = $item->unit_price;
            $purchasesInvoiceItem->quantity = $item->quantity;
            $purchasesInvoiceItem->discount = $item->discount;
            $purchasesInvoiceItem->taxes = $item->taxes;
            $purchasesInvoiceItem->purchasesInvoiceable()->associate($item->purchasesOrderable);
            $purchasesInvoiceItem->purchasesInvoice()->associate($invoice);
            $purchasesInvoiceItem->save();
        }

        return $invoice;
    }

    /**
     * @param  PurchasesOrder  $order
     * @param  array<string, string|mixed>  $payload
     * @return PurchasesInvoice|Error
     */
    private function generatePartialProductInvoice($order, $payload)
    {
        if (! isset($payload['items'])) {
            return Error::fromArray([
                'title' => 'ITEMS_ARRAY_IS_REQUIRED',
                'status' => '400',
            ]);
        }
        if (! is_array($payload['items'])) {
            return Error::fromArray([
                'title' => 'ITEMS_ARRAY_IS_REQUIRED',
                'status' => '400',
            ]);
        }

        $resolver = json_api()->getDefaultResolver();
        $remainingItems = $order->getInvoicingItemsState()['remainingItems'];
        foreach ($payload['items'] as $key => $item) {
            if ($remainingItems[$key]) {
                if ($item['quantity'] > $remainingItems[$key]['quantity']) {
                    /** @phpstan-ignore-next-line */
                    $purchaseInvoiceable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                    return Error::fromArray([
                        'title' => 'INVOICING_ITEM_QUANTITY_GREATER_THAN_REMAINING_ORDER',
                        'detail' => __(
                            'errors.invoicing_item_x_quantity_x_is_greater_than_order_quantity_x',
                            [
                                'product' => $purchaseInvoiceable->name,
                                'quantity' => $item['quantity'],
                                'remainingQuantity' => $remainingItems[$key]['quantity'],
                            ]
                        ),
                        'status' => '400',
                    ]);
                }
            } else {
                /** @phpstan-ignore-next-line */
                $purchaseInvoiceable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                return Error::fromArray([
                    'title' => 'INVOICING_ITEM_NOT_FOUND',
                    'detail' => __(
                        'errors.invoicing_item_x_not_found_in_order',
                        [
                            'product' => $purchaseInvoiceable->name,
                        ]
                    ),
                    'status' => '400',
                ]);
            }
        }

        $invoice = new PurchasesInvoice();
        $invoice->status = PurchasesInvoice::STATUS_VALIDATED;
        $invoice->invoice_type = PurchasesOrder::INVOICING_TYPE_PRODUCT;
        $invoice->expiration_time = Carbon::now()->addDays(30);
        $invoice->discounts = $order->discounts;
        $invoice->has_no_taxes = $order->has_no_taxes;
        $invoice->organization()->associate($order->organization);
        $invoice->purchasesOrder()->associate($order);
        $invoice->issuer()->associate($order->issuer);
        $invoice->destinationWarehouse()->associate($order->destinationWarehouse);
        $invoice->save();

        $invoice->allowedLocations()->sync($order->allowedLocations);

        foreach ($order->items as $item) {
            $itemQuantity = $payload['items'][$item->getOrderable()->getItemId()]['quantity'];

            $purchasesInvoiceItem = new PurchasesInvoiceItem();
            $purchasesInvoiceItem->code = $item->code;
            $purchasesInvoiceItem->excerpt = $item->excerpt;
            $purchasesInvoiceItem->unit_price = $item->unit_price;
            $purchasesInvoiceItem->quantity = $itemQuantity;
            $purchasesInvoiceItem->discount = $item->discount;
            $purchasesInvoiceItem->taxes = $item->taxes;
            $purchasesInvoiceItem->purchasesInvoiceable()->associate($item->purchasesOrderable);
            $purchasesInvoiceItem->purchasesInvoice()->associate($invoice);
            $purchasesInvoiceItem->save();
        }

        return $invoice;
    }

    /**
     * @param  PurchasesOrder  $order
     * @param  array<string, string|mixed>  $payload
     * @return PurchasesInvoice|Error
     */
    private function generatePartialAmountInvoice($order, $payload)
    {
        if (! isset($payload['items'])) {
            return Error::fromArray([
                'title' => 'ITEMS_ARRAY_IS_REQUIRED',
                'status' => '400',
            ]);
        }
        if (! is_array($payload['items'])) {
            return Error::fromArray([
                'title' => 'ITEMS_ARRAY_IS_REQUIRED',
                'status' => '400',
            ]);
        }

        $invoicingAmountsState = $order->getInvoicingAmountsState();

        if ($payload['items']['amount'] > $invoicingAmountsState['remainingInvoiceAmount']) {
            return Error::fromArray([
                'title' => 'INVOICING_AMOUNT_EXEEDS_ORDER_AMOUNT',
                'status' => '400',
            ]);
        }

        $invoice = new PurchasesInvoice();
        $invoice->status = PurchasesInvoice::STATUS_VALIDATED;
        $invoice->invoice_type = PurchasesOrder::INVOICING_TYPE_AMOUNT;
        $invoice->expiration_time = Carbon::now()->addDays(30);
        $invoice->discounts = $order->discounts;
        $invoice->has_no_taxes = $order->has_no_taxes;
        $invoice->organization()->associate($order->organization);
        $invoice->purchasesOrder()->associate($order);
        $invoice->issuer()->associate($order->issuer);
        $invoice->destinationWarehouse()->associate($order->destinationWarehouse);
        $invoice->save();

        $invoice->allowedLocations()->sync($order->allowedLocations);

        $purchasesInvoiceItem = new PurchasesInvoiceItem();
        $purchasesInvoiceItem->code = $order->code.' - INV-'.$payload['items']['invoice_type'];
        $purchasesInvoiceItem->excerpt = $payload['items']['excerpt'];
        $purchasesInvoiceItem->unit_price = $payload['items']['amount'];
        $purchasesInvoiceItem->quantity = 1;
        $purchasesInvoiceItem->discount = 0;
        $purchasesInvoiceItem->taxes = [];
        $purchasesInvoiceItem->purchasesInvoice()->associate($invoice);
        $purchasesInvoiceItem->save();

        return $invoice;
    }
}
