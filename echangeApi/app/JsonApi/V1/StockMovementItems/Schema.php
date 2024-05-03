<?php

namespace App\JsonApi\V1\StockMovementItems;

use App\Models\StockMovementItem;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'stock-movement-items';

    /**
     * @param  StockMovementItem  $resource
     *                                       the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  StockMovementItem  $resource
     *                                       the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'quantity' => $resource->quantity,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'stockMovement' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->stockMovement;
                },
            ],
            'storable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->storable;
                },
            ],
            'unitOfMeasureUnit' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->unitOfMeasureUnit;
                },
            ],
        ];
    }
}
