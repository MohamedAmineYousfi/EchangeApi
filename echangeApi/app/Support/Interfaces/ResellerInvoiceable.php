<?php

namespace App\Support\Interfaces;

use App\Models\ResellerInvoiceItem;

interface ResellerInvoiceable
{
    public function handleResellerInvoiceValidated(ResellerInvoiceItem $resellerInvoiceItem): void;

    public function handleResellerInvoicePaied(ResellerInvoiceItem $resellerInvoiceItem): void;

    public function handleResellerInvoiceCanceled(ResellerInvoiceItem $resellerInvoiceItem): void;
}
