<?php

namespace App\Support\Interfaces;

use App\Models\PurchasesInvoiceItem;

interface PurchasesInvoiceable extends Invoiceable
{
    public function handlePurchasesInvoiceValidated(PurchasesInvoiceItem $purchasesInvoiceItem): void;

    public function handlePurchasesInvoicePaied(PurchasesInvoiceItem $purchasesInvoiceItem): void;

    public function handlePurchasesInvoiceCanceled(PurchasesInvoiceItem $purchasesInvoiceItem): void;
}
