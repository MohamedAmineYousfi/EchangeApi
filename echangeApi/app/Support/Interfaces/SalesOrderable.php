<?php

namespace App\Support\Interfaces;

use App\Models\SalesOrderItem;

interface SalesOrderable extends Orderable
{
    public function handleSalesOrderValidated(SalesOrderItem $salesOrderItem): void;

    public function handleSalesOrderCanceled(SalesOrderItem $salesOrderItem): void;
}
