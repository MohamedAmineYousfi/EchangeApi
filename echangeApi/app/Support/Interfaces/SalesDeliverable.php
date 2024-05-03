<?php

namespace App\Support\Interfaces;

use App\Models\SalesDeliveryItem;

interface SalesDeliverable extends Deliverable
{
    public function handleSalesDeliveryValidated(SalesDeliveryItem $salesDeliveryItem): void;

    public function handleSalesDeliveryCanceled(SalesDeliveryItem $salesDeliveryItem): void;
}
