<?php

namespace App\Http\Controllers\Api\V1\SalesOrder;

use App\Http\Requests\Api\V1\SalesOrder\GenerateInvoiceRequest;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
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
    public function generate(GenerateInvoiceRequest $request, SalesOrder $order)
    {
        if ($order->status != SalesOrder::STATUS_VALIDATED) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'ORDER_STATUS_NOT_VALIDATED',
                    'status' => '400',
                ]),
            ]);
        }
        if ($order->invoicing_status == SalesOrder::INVOICING_STATUS_INVOICED) {
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
            $order->invoicing_type = SalesOrder::INVOICING_TYPE_PRODUCT;
            $order->save();
        } elseif ($payload['invoicingType'] == SalesOrder::INVOICING_TYPE_PRODUCT) {
            $invoice = $this->generatePartialProductInvoice($order, $payload);
            if (! $invoice instanceof SalesInvoice) {
                return $this->reply()->errors([$invoice]);
            }
            $order->invoicing_type = SalesOrder::INVOICING_TYPE_PRODUCT;
            $order->save();
        } elseif ($payload['invoicingType'] == SalesOrder::INVOICING_TYPE_AMOUNT) {
            $invoice = $this->generatePartialAmountInvoice($order, $payload);
            if (! $invoice instanceof SalesInvoice) {
                return $this->reply()->errors([$invoice]);
            }
            $order->invoicing_type = SalesOrder::INVOICING_TYPE_AMOUNT;
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
        $invoice = new SalesInvoice();
        $invoice->status = SalesInvoice::STATUS_VALIDATED;
        $invoice->invoice_type = SalesOrder::INVOICING_TYPE_PRODUCT;
        $invoice->expiration_time = Carbon::now()->addDays(30);
        $invoice->discounts = $order->discounts;
        $invoice->has_no_taxes = $order->has_no_taxes;
        $invoice->billing_company_name = $order->billing_company_name;
        $invoice->billing_firstname = $order->billing_firstname;
        $invoice->billing_lastname = $order->billing_lastname;
        $invoice->billing_country = $order->billing_country;
        $invoice->billing_state = $order->billing_state;
        $invoice->billing_city = $order->billing_city;
        $invoice->billing_zipcode = $order->billing_zipcode;
        $invoice->billing_address = $order->billing_address;
        $invoice->billing_email = $order->billing_email;
        $invoice->organization()->associate($order->organization);
        $invoice->salesOrder()->associate($order);
        $invoice->recipient()->associate($order->recipient);
        $invoice->sourceWarehouse()->associate($order->sourceWarehouse);
        $invoice->save();

        $invoice->allowedLocations()->sync($order->allowedLocations);

        foreach ($order->items as $item) {
            $salesInvoiceItem = new SalesInvoiceItem();
            $salesInvoiceItem->code = $item->code;
            $salesInvoiceItem->excerpt = $item->excerpt;
            $salesInvoiceItem->unit_price = $item->unit_price;
            $salesInvoiceItem->quantity = $item->quantity;
            $salesInvoiceItem->discount = $item->discount;
            $salesInvoiceItem->taxes = $item->taxes;
            $salesInvoiceItem->salesInvoiceable()->associate($item->salesOrderable);
            $salesInvoiceItem->salesInvoice()->associate($invoice);
            $salesInvoiceItem->save();
        }

        return $invoice;
    }

    /**
     * @param  SalesOrder  $order
     * @param  array<string, string|mixed>  $payload
     * @return SalesInvoice|Error
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
                    $saleInvoiceable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                    return Error::fromArray([
                        'title' => 'INVOICING_ITEM_QUANTITY_GREATER_THAN_REMAINING_ORDER',
                        'detail' => __(
                            'errors.invoicing_item_x_quantity_x_is_greater_than_order_quantity_x',
                            [
                                'product' => $saleInvoiceable->name,
                                'quantity' => $item['quantity'],
                                'remainingQuantity' => $remainingItems[$key]['quantity'],
                            ]
                        ),
                        'status' => '400',
                    ]);
                }
            } else {
                /** @phpstan-ignore-next-line */
                $saleInvoiceable = $resolver->getType($remainingItems[$key]['item_type'])::find($key);

                return Error::fromArray([
                    'title' => 'INVOICING_ITEM_NOT_FOUND',
                    'detail' => __(
                        'errors.invoicing_item_x_not_found_in_order',
                        [
                            'product' => $saleInvoiceable->name,
                        ]
                    ),
                    'status' => '400',
                ]);
            }
        }

        $invoice = new SalesInvoice();
        $invoice->status = SalesInvoice::STATUS_VALIDATED;
        $invoice->invoice_type = SalesOrder::INVOICING_TYPE_PRODUCT;
        $invoice->expiration_time = Carbon::now()->addDays(30);
        $invoice->discounts = $order->discounts;
        $invoice->has_no_taxes = $order->has_no_taxes;
        $invoice->billing_company_name = $order->billing_company_name;
        $invoice->billing_firstname = $order->billing_firstname;
        $invoice->billing_lastname = $order->billing_lastname;
        $invoice->billing_country = $order->billing_country;
        $invoice->billing_state = $order->billing_state;
        $invoice->billing_city = $order->billing_city;
        $invoice->billing_zipcode = $order->billing_zipcode;
        $invoice->billing_address = $order->billing_address;
        $invoice->billing_email = $order->billing_email;
        $invoice->organization()->associate($order->organization);
        $invoice->salesOrder()->associate($order);
        $invoice->recipient()->associate($order->recipient);
        $invoice->sourceWarehouse()->associate($order->sourceWarehouse);
        $invoice->save();

        $invoice->allowedLocations()->sync($order->allowedLocations);

        foreach ($order->items as $item) {
            $itemQuantity = $payload['items'][$item->getOrderable()->getItemId()]['quantity'];

            $salesInvoiceItem = new SalesInvoiceItem();
            $salesInvoiceItem->code = $item->code;
            $salesInvoiceItem->excerpt = $item->excerpt;
            $salesInvoiceItem->unit_price = $item->unit_price;
            $salesInvoiceItem->quantity = $itemQuantity;
            $salesInvoiceItem->discount = $item->discount;
            $salesInvoiceItem->taxes = $item->taxes;
            $salesInvoiceItem->salesInvoiceable()->associate($item->salesOrderable);
            $salesInvoiceItem->salesInvoice()->associate($invoice);
            $salesInvoiceItem->save();
        }

        return $invoice;
    }

    /**
     * @param  SalesOrder  $order
     * @param  array<string, string|mixed>  $payload
     * @return SalesInvoice|Error
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

        $invoice = new SalesInvoice();
        $invoice->status = SalesInvoice::STATUS_VALIDATED;
        $invoice->invoice_type = SalesOrder::INVOICING_TYPE_AMOUNT;
        $invoice->expiration_time = Carbon::now()->addDays(30);
        $invoice->discounts = $order->discounts;
        $invoice->has_no_taxes = $order->has_no_taxes;
        $invoice->billing_entity_type = $order->billing_entity_type;
        $invoice->billing_company_name = $order->billing_company_name;
        $invoice->billing_firstname = $order->billing_firstname;
        $invoice->billing_lastname = $order->billing_lastname;
        $invoice->billing_country = $order->billing_country;
        $invoice->billing_state = $order->billing_state;
        $invoice->billing_city = $order->billing_city;
        $invoice->billing_zipcode = $order->billing_zipcode;
        $invoice->billing_address = $order->billing_address;
        $invoice->billing_email = $order->billing_email;
        $invoice->organization()->associate($order->organization);
        $invoice->salesOrder()->associate($order);
        $invoice->recipient()->associate($order->recipient);
        $invoice->sourceWarehouse()->associate($order->sourceWarehouse);
        $invoice->save();

        $invoice->allowedLocations()->sync($order->allowedLocations);

        $salesInvoiceItem = new SalesInvoiceItem();
        $salesInvoiceItem->code = $order->code.' - INV-'.$payload['items']['invoice_type'];
        $salesInvoiceItem->excerpt = $payload['items']['excerpt'];
        $salesInvoiceItem->unit_price = $payload['items']['amount'];
        $salesInvoiceItem->quantity = 1;
        $salesInvoiceItem->discount = 0;
        $salesInvoiceItem->taxes = [];
        $salesInvoiceItem->salesInvoice()->associate($invoice);
        $salesInvoiceItem->save();

        return $invoice;
    }
}
