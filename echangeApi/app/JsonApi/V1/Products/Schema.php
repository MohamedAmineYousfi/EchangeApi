<?php

namespace App\JsonApi\V1\Products;

use App\Models\Product;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'products';

    /**
     * @param  Product  $resource
     *                             the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Product  $resource
     *                             the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        $filters = request()->query('filter');
        $filters = $filters ? $filters : [];

        $supplierProduct = null;
        if (isset($filters['supplier'])) {
            $supplierProduct = $resource->supplierProducts()->where('supplier_id', '=', $filters['supplier'])->first();
        }
        $warehouseProduct = null;
        if (isset($filters['warehouse'])) {
            $warehouseProduct = $resource->warehouseProducts()->where('warehouse_id', '=', $filters['warehouse'])->first();
        }

        return [
            'code' => $resource->code,
            'name' => $resource->name,
            'sku' => $resource->sku,
            'excerpt' => $resource->excerpt ?: '',
            'selling_price' => $resource->selling_price,
            'buying_price' => $resource->buying_price,
            'picture' => $resource->picture,
            'gallery' => $resource->gallery,
            'denomination' => $resource->getDenomination(),
            'supplierProduct' => $supplierProduct,
            'warehouseProduct' => $warehouseProduct,
            'status' => $resource->status,
            'can_be_sold' => boolval($resource->can_be_sold),
            'can_be_purchased' => boolval($resource->can_be_purchased),
            'product_id' => strval($resource->id),
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
            'organization' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
            'allowedLocations' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['allowedLocations']),
                self::DATA => function () use ($item) {
                    return $item->allowedLocations ? $item->allowedLocations : [];
                },
            ],
            'supplierProducts' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['supplierProducts']),
                self::DATA => function () use ($item) {
                    return $item->supplierProducts ? $item->supplierProducts : [];
                },
            ],
            'warehouseProducts' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['warehouseProducts']),
                self::DATA => function () use ($item) {
                    return $item->warehouseProducts ? $item->warehouseProducts : [];
                },
            ],
            'categories' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['allowedLocations']),
                self::DATA => function () use ($item) {
                    return $item->categories;
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
            'unitOfMeasure' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->unitOfMeasure;
                },
            ],
        ];
    }
}
