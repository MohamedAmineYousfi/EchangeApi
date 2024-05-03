<?php

namespace App\Constants;

class PropertyInformation
{
    public const TYPE_COMMERCIAL = 'COMMERCIAL';

    public const TYPE_INDUSTRIAL = 'INDUSTRIAL';

    public const TYPE_VACANT_LAND = 'VACANT_LAND';

    public const TYPE_RESIDENTIAL = 'RESIDENTIAL';

    const TYPES = [self::TYPE_COMMERCIAL, self::TYPE_INDUSTRIAL, self::TYPE_VACANT_LAND, self::TYPE_RESIDENTIAL];

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_CANCEL = 'CANCEL';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_CONFIRMED = 'CONFIRMED';

    const STATUS = [self::STATUS_ACTIVE, self::STATUS_CANCEL, self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_APPROVED];

    public const TAXES_MUNICIPAL = 'MUNICIPAL';

    public const TAXES_SCHOOL = 'SCHOOL';

    const TAXES_DUES = [self::TAXES_MUNICIPAL, self::TAXES_SCHOOL];

    public const PAYMENT_TYPE_PAYMENT = 'PAYMENT';
    public const PAYMENT_TYPE_REFUND = 'REFUND';
    const PAYMENTS_TYPE_LIST = [self::PAYMENT_TYPE_REFUND, self::PAYMENT_TYPE_PAYMENT];
}
