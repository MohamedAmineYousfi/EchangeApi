<?php

namespace App\JsonApi\V1\Locations;

use App\Models\Location;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'locations';

    /**
     * @param  Location  $resource
     *                              the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Location  $resource
     *                              the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'name' => $resource->name,
            'is_municipal' => (bool) $resource->is_municipal,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
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
            'manager' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['manager']),
                self::DATA => function () use ($item) {
                    return $item->manager;
                },
            ],
            'contacts' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['contacts']),
                self::DATA => function () use ($item) {
                    return $item->contacts ?? [];
                },
            ],
            'users' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['users']),
                self::DATA => function () use ($item) {
                    return $item->users ?? [];
                },
            ],
        ];
    }
}
