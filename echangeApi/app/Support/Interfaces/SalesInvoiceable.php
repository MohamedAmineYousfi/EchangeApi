<?php

namespace App\Support\Interfaces;

use App\Models\SalesInvoiceItem;

interface SalesInvoiceable extends Invoiceable
{
    public function handleSalesInvoiceValidated(SalesInvoiceItem $salesInvoiceItem): void;

    public function handleSalesInvoicePaied(SalesInvoiceItem $salesInvoiceItem): void;

    public function handleSalesInvoiceCanceled(SalesInvoiceItem $salesInvoiceItem): void;
}
