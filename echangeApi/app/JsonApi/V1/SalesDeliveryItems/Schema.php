<?php

namespace App\JsonApi\V1\SalesDeliveryItems;

use App\Models\SalesDeliveryItem;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'sales-delivery-items';

    /**
     * @param  SalesDeliveryItem  $resource
     *                                       the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  SalesDeliveryItem  $resource
     *                                       the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'code' => $resource->code,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'quantity' => $resource->quantity,
            'expected_quantity' => $resource->expected_quantity,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'salesDelivery' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->salesDelivery;
                },
            ],
            'salesDeliverable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->salesDeliverable;
                },
            ],
        ];
    }
}
