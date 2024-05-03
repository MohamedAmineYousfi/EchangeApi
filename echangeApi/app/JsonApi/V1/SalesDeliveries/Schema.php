<?php

namespace App\JsonApi\V1\SalesDeliveries;

use App\Models\SalesDelivery;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'sales-deliveries';

    /**
     * @param  SalesDelivery  $resource
     *                                   the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  SalesDelivery  $resource
     *                                   the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'code' => $resource->code,
            'expiration_time' => optional($resource->expiration_time)->format('Y-m-d H:i:s'),
            'status' => $resource->status,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,

            ...$resource->getDeliveryInformations(),
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'items' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['items']),
                self::DATA => function () use ($item) {
                    return $item->items;
                },
            ],
            'recipient' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->recipient;
                },
            ],
            'organization' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
            'salesOrder' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['salesOrder']),
                self::DATA => function () use ($item) {
                    return $item->salesOrder;
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
            'sourceWarehouse' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['sourceWarehouse']),
                self::DATA => function () use ($item) {
                    return $item->sourceWarehouse;
                },
            ],
        ];
    }
}
