<?php

namespace App\Support\Interfaces;

use App\Models\PurchasesDeliveryItem;

interface PurchasesDeliverable extends Deliverable
{
    public function handlePurchasesDeliveryValidated(PurchasesDeliveryItem $purchasesDeliveryItem): void;

    public function handlePurchasesDeliveryCanceled(PurchasesDeliveryItem $purchasesDeliveryItem): void;
}
