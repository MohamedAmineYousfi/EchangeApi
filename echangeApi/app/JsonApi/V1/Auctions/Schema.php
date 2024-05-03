<?php

namespace App\JsonApi\V1\Auctions;

use App\Models\Auction;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'auctions';

    /**
     * @param  Auction  $resource
     *                             the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Auction  $resource
     *                             the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'excerpt' => $resource->excerpt,
            'object_type' => $resource->object_type,
            'total_properties' => $resource->properties()->count(),
            'pre_opening_at' => $resource->pre_opening_at,
            'extension_time' => $resource->extension_time,
            'delay' => $resource->delay,
            'authorized_payments' => $resource->authorized_payments,
            'start_at' => $resource->start_at,
            'listings_registrations_close_at' => $resource->listings_registrations_close_at,
            'listings_registrations_open_at' => $resource->listings_registrations_open_at,
            'end_at' => $resource->end_at,
            'activated_timer' => (bool) $resource->activated_timer,
            'auction_type' => $resource->auction_type,
            'country' => $resource->country,
            'state' => $resource->state,
            'city' => $resource->city,
            'zipcode' => $resource->zipcode,
            'address' => $resource->address,
            'lat' => $resource->lat,
            'long' => $resource->long,
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
            'managers' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['managers']),
                self::DATA => function () use ($item) {
                    return $item->managers ?? [];
                },
            ],
        ];
    }
}
