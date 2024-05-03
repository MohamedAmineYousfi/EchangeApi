<?php

namespace App\Support\Interfaces;

use App\Models\PurchasesOrderItem;

interface PurchasesOrderable extends Orderable
{
    public function handlePurchasesOrderValidated(PurchasesOrderItem $purchasesOrderItem): void;

    public function handlePurchasesOrderCanceled(PurchasesOrderItem $purchasesOrderItem): void;
}
