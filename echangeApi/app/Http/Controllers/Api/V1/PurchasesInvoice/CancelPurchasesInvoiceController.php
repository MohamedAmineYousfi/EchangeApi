<?php

namespace App\Http\Controllers\Api\V1\PurchasesInvoice;

use App\Http\Requests\Api\V1\PurchasesInvoice\CancelPurchasesInvoiceRequest;
use App\Models\PurchasesInvoice;
use App\Models\PurchasesInvoiceItem;
use App\Support\Interfaces\PurchasesInvoiceable;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelPurchasesInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelPurchasesInvoiceRequest $request, PurchasesInvoice $invoice)
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

        if ($invoice->status !== PurchasesInvoice::STATUS_CANCELED) {
            $invoice->status = PurchasesInvoice::STATUS_CANCELED;
            $invoice->save();

            foreach ($invoice->items as $item) {
                /** @var PurchasesInvoiceItem $item */
                $invoiceable = $item->getInvoiceable();
                if ($invoiceable instanceof PurchasesInvoiceable) {
                    $invoiceable->handlePurchasesInvoiceCanceled($item);
                }
            }
        }

        return $this->reply()->content($invoice);
    }
}
