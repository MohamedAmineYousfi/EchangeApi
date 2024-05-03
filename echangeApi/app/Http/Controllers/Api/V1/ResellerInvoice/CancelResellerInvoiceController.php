<?php

namespace App\Http\Controllers\Api\V1\ResellerInvoice;

use App\Http\Requests\Api\V1\ResellerInvoice\CancelResellerInvoiceRequest;
use App\Models\ResellerInvoice;
use App\Models\ResellerInvoiceItem;
use App\Support\Interfaces\ResellerInvoiceable;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class CancelResellerInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function cancel(CancelResellerInvoiceRequest $request, ResellerInvoice $invoice)
    {
        if ($invoice->status !== ResellerInvoice::STATUS_CANCELED) {
            $invoice->status = ResellerInvoice::STATUS_CANCELED;
            $invoice->save();

            foreach ($invoice->items as $item) {
                /** @var ResellerInvoiceItem $item */
                $invoiceable = $item->getInvoiceable();
                if ($invoiceable instanceof ResellerInvoiceable) {
                    $invoiceable->handleResellerInvoiceCanceled($item);
                }
            }
        }

        return $this->reply()->content($invoice);
    }
}
