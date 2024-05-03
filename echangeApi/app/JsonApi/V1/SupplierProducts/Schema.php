<?php

namespace App\JsonApi\V1\SupplierProducts;

use App\Models\SupplierProduct;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'supplier-products';

    /**
     * @param  SupplierProduct  $resource
     *                                     the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  SupplierProduct  $resource
     *                                     the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'sku' => $resource->sku,
            'excerpt' => $resource->excerpt ?: '',
            'selling_price' => $resource->getSellingPrice(),
            'buying_price' => $resource->getBuyingPrice(),
            'name' => $resource->product->name,
            'product_id' => strval($resource->product->id),
            'custom_pricing' => boolval($resource->custom_pricing),
            'custom_taxation' => boolval($resource->custom_taxation),
            'can_be_sold' => boolval($resource->product->can_be_sold),
            'can_be_purchased' => boolval($resource->product->can_be_purchased),
            'supplier_code' => $resource->supplier_code,
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * getRelationships
     *
     * @param  mixed  $item
     * @param  mixed  $isPrimary
     * @param  array  $includeRelationships
     * @return array
     */
    public function getRelationships($item, $isPrimary, $includeRelationships)
    {
        return [
            'product' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['product']),
                self::DATA => function () use ($item) {
                    return $item->product;
                },
            ],
            'supplier' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['supplier']),
                self::DATA => function () use ($item) {
                    return $item->supplier;
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
