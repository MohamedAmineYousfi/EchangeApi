<?php

namespace App\Http\Controllers\Api\V1\PurchasesInvoice;

use App\Http\Requests\Api\V1\PurchasesInvoice\ValidatePurchasesInvoiceRequest;
use App\Models\PurchasesInvoice;
use App\Models\PurchasesInvoiceItem;
use App\Support\Interfaces\PurchasesInvoiceable;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ValidatePurchasesInvoiceController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function validate(ValidatePurchasesInvoiceRequest $request, PurchasesInvoice $invoice)
    {
        if ($invoice->status == PurchasesInvoice::STATUS_DRAFT) {
            $invoice->status = PurchasesInvoice::STATUS_VALIDATED;
            $invoice->save();

            foreach ($invoice->items as $item) {
                /** @var PurchasesInvoiceItem $item */
                $invoiceable = $item->getInvoiceable();
                if ($invoiceable instanceof PurchasesInvoiceable) {
                    $invoiceable->handlePurchasesInvoiceValidated($item);
                }
            }
        }

        return $this->reply()->content($invoice);
    }
}
