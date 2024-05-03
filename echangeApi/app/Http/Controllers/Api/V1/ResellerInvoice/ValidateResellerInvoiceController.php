<?php

namespace App\Http\Controllers\Api\V1\ResellerInvoice;

use App\Http\Requests\Api\V1\ResellerInvoice\ValidateResellerInvoiceRequest;
use App\Models\ResellerInvoice;
use App\Models\ResellerInvoiceItem;
use App\Support\Interfaces\ResellerInvoiceable;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ValidateResellerInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidateResellerInvoiceRequest $request, ResellerInvoice $invoice)
    {
        if ($invoice->status == ResellerInvoice::STATUS_DRAFT) {
            $invoice->status = ResellerInvoice::STATUS_VALIDATED;
            $invoice->save();

            foreach ($invoice->items as $item) {
                /** @var ResellerInvoiceItem $item */
                $invoiceable = $item->getInvoiceable();
                if ($invoiceable instanceof ResellerInvoiceable) {
                    $invoiceable->handleResellerInvoiceValidated($item);
                }
            }
        }

        return $this->reply()->content($invoice);
    }
}
