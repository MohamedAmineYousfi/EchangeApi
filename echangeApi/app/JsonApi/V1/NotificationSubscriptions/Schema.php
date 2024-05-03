<?php

namespace App\JsonApi\V1\NotificationSubscriptions;

use App\Models\Notification;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'notification-subscriptions';

    /**
     * @param  Notification  $resource
     *                                  the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Notification  $resource
     *                                  the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'event' => $resource->event,
            'notification_type' => $resource->notification_type,
            'object_type' => $resource->object_type,
            'object_id' => $resource->object_id,
            'updated_at' => $resource->updated_at,
            'created_at' => $resource->created_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships): array
    {
        return [];
    }
}
