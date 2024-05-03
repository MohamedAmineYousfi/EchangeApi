<?php

namespace App\JsonApi\V1\SalesOrderItems;

use App\Models\SalesOrderItem;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'sales-order-items';

    /**
     * @param  SalesOrderItem  $resource
     *                                    the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  SalesOrderItem  $resource
     *                                    the domain record being serialized.
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
            'salesOrder' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->salesOrder;
                },
            ],
            'salesOrderable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->salesOrderable;
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
