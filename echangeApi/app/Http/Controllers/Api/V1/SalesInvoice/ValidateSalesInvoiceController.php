<?php

namespace App\Http\Controllers\Api\V1\SalesInvoice;

use App\Http\Requests\Api\V1\SalesInvoice\ValidateSalesInvoiceRequest;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Support\Interfaces\SalesInvoiceable;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ValidateSalesInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidateSalesInvoiceRequest $request, SalesInvoice $invoice)
    {
        if ($invoice->status == SalesInvoice::STATUS_DRAFT) {
            $invoice->status = SalesInvoice::STATUS_VALIDATED;
            $invoice->save();

            foreach ($invoice->items as $item) {
                /** @var SalesInvoiceItem $item */
                $invoiceable = $item->getInvoiceable();
                if ($invoiceable instanceof SalesInvoiceable) {
                    $invoiceable->handleSalesInvoiceValidated($item);
                }
            }
        }

        return $this->reply()->content($invoice);
    }
}
