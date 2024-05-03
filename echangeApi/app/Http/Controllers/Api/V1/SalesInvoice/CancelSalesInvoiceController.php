<?php

namespace App\Http\Controllers\Api\V1\SalesInvoice;

use App\Http\Requests\Api\V1\SalesInvoice\CancelSalesInvoiceRequest;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Support\Interfaces\SalesInvoiceable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelSalesInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelSalesInvoiceRequest $request, SalesInvoice $invoice)
    {
        if ($invoice->payments->count() > 0) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'INVOICE_HAS_COMPLETED_PAYMENTS',
                    'detail' => __(
                        'errors.this_invoice_has_completed_payments',
                    ),
                    'status' => '400',
                ]),
            ]);
        }

        if ($invoice->status !== SalesInvoice::STATUS_CANCELED) {
            $invoice->status = SalesInvoice::STATUS_CANCELED;
            $invoice->save();

            foreach ($invoice->items as $item) {
                /** @var SalesInvoiceItem $item */
                $invoiceable = $item->getInvoiceable();
                if ($invoiceable instanceof SalesInvoiceable) {
                    $invoiceable->handleSalesInvoiceCanceled($item);
                }
            }
        }

        return $this->reply()->content($invoice);
    }
}
