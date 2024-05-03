<?php

namespace App\JsonApi\V1\Bids;

use App\Models\Bid;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'bids';

    /**
     * @param  Bid  $resource
     *                         the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Bid  $resource
     *                         the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'ip_address' => $resource->ip_address,
            'bid' => $resource->bid,
            'max_bid' => $resource->max_bid,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
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
            'user' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['user']),
                self::DATA => function () use ($item) {
                    return $item->user ?? [];
                },
            ],
            'createdBy' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['createdBy']),
                self::DATA => function () use ($item) {
                    return $item->createdBy ?? [];
                },
            ],
            'property' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['property']),
                self::DATA => function () use ($item) {
                    return $item->property ?? [];
                },
            ],
        ];
    }
}
