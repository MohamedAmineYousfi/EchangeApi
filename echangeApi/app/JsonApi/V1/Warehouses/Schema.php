<?php

namespace App\JsonApi\V1\Warehouses;

use App\Models\Warehouse;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'warehouses';

    /**
     * @param  Warehouse  $resource
     *                               the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Warehouse  $resource
     *                               the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'name' => $resource->name,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'results' => $resource['results'],
            'is_model' => (bool) $resource->is_model,
            'allow_negative_inventory' => (bool) $resource->allow_negative_inventory,
            'allow_unregistered_products' => (bool) $resource->allow_unregistered_products,
            'use_warehouse_taxes' => (bool) $resource->use_warehouse_taxes,
            'applied_at' => $resource->applied_at,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships): array
    {
        return [
            'organization' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
            'modelUsed' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['modelUsed']),
                self::DATA => function () use ($item) {
                    return $item->modelUsed;
                },
            ],
            'allowedLocations' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['allowedLocations']),
                self::DATA => function () use ($item) {
                    return $item->allowedLocations ? $item->allowedLocations : [];
                },
            ],
            'usedBy' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['usedBy']),
                self::DATA => function () use ($item) {
                    return $item->usedBy ?: [];
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
