<?php

namespace App\JsonApi\V1\PurchasesOrderItems;

use App\Models\PurchasesOrderItem;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'purchases-order-items';

    /**
     * @param  PurchasesOrderItem  $resource
     *                                        the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  PurchasesOrderItem  $resource
     *                                        the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'code' => $resource->code,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'unit_price' => $resource->unit_price,
            'quantity' => $resource->quantity,
            'discount' => $resource->discount,
            'pricing' => [
                'subtotal' => $resource->getItemSubTotalAmount(),
                'discount_amount' => $resource->getItemDiscountAmount(),
                'taxable_amount' => $resource->getTaxableBaseAmount(),
                'taxes' => $resource->getItemTaxes(),
                'total' => $resource->getItemTotalAmount(),
            ],
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'purchasesOrder' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->purchasesOrder;
                },
            ],
            'purchasesOrderable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->purchasesOrderable;
                },
            ],
            'taxGroups' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['taxGroups']),
                self::DATA => function () use ($item) {
                    return $item->taxGroups;
                },
            ],
        ];
    }
}
