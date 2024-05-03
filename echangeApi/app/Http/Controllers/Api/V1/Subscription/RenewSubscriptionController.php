<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Requests\Api\V1\Subscription\RenewSubscriptionRequest;
use App\Models\Organization;
use App\Models\ResellerInvoice;
use App\Models\ResellerInvoiceItem;
use App\Models\Subscription;
use Carbon\Carbon;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class RenewSubscriptionController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function renew(RenewSubscriptionRequest $request, Subscription $subscription)
    {
        /** @var Organization */
        $organization = $subscription->organization;
        $invoice = new ResellerInvoice([
            'expiration_time' => Carbon::now()->addMonth(),
            'excerpt' => '',
            'status' => ResellerInvoice::STATUS_DRAFT,
            'discounts' => [],
            'organization' => null,
            ...$organization->getBillingInformations(),
        ]);
        $invoice->recipient()->associate($organization);
        $invoice->save();

        $package = $subscription->package;
        $invoiceItem = new ResellerInvoiceItem();
        $invoiceItem->code = $package->code;
        $invoiceItem->excerpt = "$package->code - $package->name";
        $invoiceItem->unit_price = $package->price;
        $invoiceItem->quantity = 1;
        $invoiceItem->discount = 0;
        $invoiceItem->resellerInvoice()->associate($invoice);
        $invoiceItem->resellerInvoiceable()->associate($package);
        $invoiceItem->save();

        return $this->reply()->content($invoice);
    }
}
