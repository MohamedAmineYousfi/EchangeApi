<?php

namespace App\JsonApi\V1\Notifications;

use App\Models\Notification;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'notifications';

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
            'notification_type' => $resource->type,
            'data' => $resource->data,
            'read_at' => $resource->read_at,
            'updated_at' => $resource->updated_at,
            'created_at' => $resource->created_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships): array
    {
        return [
            'notifiable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['notifiable']),
                self::DATA => function () use ($item) {
                    return $item->notifiable;
                },
            ],
        ];
    }
}
