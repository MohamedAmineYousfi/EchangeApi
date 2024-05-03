<?php

namespace App\Mail\ResellerInvoice;

use App\Models\ResellerInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelResellerInvoice extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The invoice instance.
     *
     * @var ResellerInvoice
     */
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param  ResellerInvoice  $invoice
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Set the subject for the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildSubject($message)
    {
        $message->subject('['.config('app.name').'] Facture #'.$this->invoice->code.' annulée');

        return $this;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invoice.cancel');
    }
}
