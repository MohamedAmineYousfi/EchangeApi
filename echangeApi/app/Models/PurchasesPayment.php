<?php

namespace App\Models;

use App\Support\Classes\BasePayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasesPayment extends BasePayment
{
    public function handlePaymentCompleted()
    {
        $this->handlePurchasesPaymentCompleted();
    }

    public function send()
    {
    }

    public function getInvoice(): PurchasesInvoice
    {
        return $this->invoice;
    }

    /**
     * An InvoiceItem belongs to an Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchasesInvoice::class, 'purchases_invoice_id');
    }

    public function scopeInvoice($query, $invoice): Builder
    {
        return $query->where('purchases_invoice_id', $invoice);
    }

    /**
     * Get the invoice total amount
     *
     * @return void
     */
    public function refreshPayment()
    {
        if (! $this->code) {
            $this->date = Carbon::now();
            $paymentsCount = PurchasesPayment::withoutGlobalScopes()->count() + 1;
            $this->code = 'P-PAY-'.Carbon::now()->format('Ymd').str_pad(strval($paymentsCount), 10, '0', STR_PAD_LEFT);
            $this->organization()->associate($this->invoice->organization);
        }
    }

    public function handlePurchasesPaymentCompleted()
    {
        if ($this->status === self::STATUS_COMPLETED) {
            /** @var PurchasesInvoice $invoice */
            $invoice = $this->invoice;
            if (bccomp(strval($invoice->getInvoiceTotalAmount()), strval($invoice->getInvoiceTotalPaied())) == 0) {
                $invoice->handleInvoicePaied();
            }
        }
    }
}
