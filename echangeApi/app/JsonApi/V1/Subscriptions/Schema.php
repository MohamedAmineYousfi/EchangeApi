<?php

namespace App\JsonApi\V1\Subscriptions;

use App\Models\Subscription;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'subscriptions';

    /**
     * @param  Subscription  $resource
     *                                  the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Subscription  $resource
     *                                  the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'code' => $resource->code,
            'start_time' => $resource->start_time->format('Y-m-d H:i:s'),
            'end_time' => $resource->end_time->format('Y-m-d H:i:s'),
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * getRelationships
     *
     * @param  mixed  $ressource
     * @param  mixed  $isPrimary
     */
    public function getRelationships($ressource, $isPrimary, array $includeRelationships): array
    {
        return [
            'package' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['package']),
                self::DATA => function () use ($ressource) {
                    return $ressource->package;
                },
            ],
            'organization' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($ressource) {
                    return $ressource->organization;
                },
            ],
        ];
    }
}
