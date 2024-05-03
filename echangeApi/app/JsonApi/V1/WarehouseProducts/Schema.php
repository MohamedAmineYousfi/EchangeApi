<?php

namespace App\JsonApi\V1\WarehouseProducts;

use App\Models\WarehouseProduct;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'warehouse-products';

    /**
     * @param  WarehouseProduct  $resource
     *                                      the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  WarehouseProduct  $resource
     *                                      the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'quantity' => $resource->quantity,
            'selling_price' => $resource->getSellingPrice(),
            'buying_price' => $resource->getBuyingPrice(),
            'sku' => $resource->sku,
            'name' => $resource->product->name,
            'product_id' => strval($resource->product->id),
            'custom_pricing' => boolval($resource->custom_pricing),
            'custom_taxation' => boolval($resource->custom_taxation),
            'can_be_sold' => boolval($resource->product->can_be_sold),
            'can_be_purchased' => boolval($resource->product->can_be_purchased),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships): array
    {
        return [
            'warehouse' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['warehouse']),
                self::DATA => function () use ($item) {
                    return $item->warehouse;
                },
            ],
            'product' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['product']),
                self::DATA => function () use ($item) {
                    return $item->product;
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
            'unitOfMeasureUnit' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['unitOfMeasureUnit']),
                self::DATA => function () use ($item) {
                    return $item->unitOfMeasureUnit;
                },
            ],
        ];
    }
}
