<?php

namespace App\Support\Interfaces;

use App\Support\Classes\BaseInvoiceItem;

interface BaseInvoiceable
{
    /**
     * getDenomination
     */
    public function getDenomination(): string;

    /**
     * handleBaseInvoiceItemCreated
     */
    public function handleBaseInvoiceItemCreated(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemCreated
     */
    public function handleBaseInvoiceItemCreating(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemCreated
     */
    public function handleBaseInvoiceItemUpdated(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemCreated
     */
    public function handleBaseInvoiceItemUpdating(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemCreated
     */
    public function handleBaseInvoiceItemDeleted(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemCreated
     */
    public function handleBaseInvoiceItemDeleting(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemPaied
     */
    public function handleInvoicePaied(BaseInvoiceItem $invoiceItem): void;

    /**
     * handleBaseInvoiceItemCompleted
     */
    public function handleInvoiceCanceled(BaseInvoiceItem $invoiceItem): void;
}
