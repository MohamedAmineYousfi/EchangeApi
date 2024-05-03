<?php

namespace App\Constants;

class ProductsInformation
{
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_DELETED = 'DELETED';

    const STATUS = [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED];
}
