<?php

namespace App\JsonApi\V1\Roles;

use App\Models\Role;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'roles';

    /**
     * @param  object  $resource
     *                            the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  object  $resource
     *                            the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param  Role  $item
     * @param  bool  $isPrimary
     * @return array
     */
    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'permissions' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['permissions']),
                self::DATA => function () use ($item) {
                    return $item->permissions;
                },
            ],
            'organization' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
            'reseller' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['reseller']),
                self::DATA => function () use ($item) {
                    return $item->reseller;
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
        ];
    }
}
