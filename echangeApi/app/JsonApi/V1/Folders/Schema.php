<?php

namespace App\JsonApi\V1\Folders;

use App\Models\Folder;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'folders';

    /**
     * @param  Folder  $resource
     *                            the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Folder  $resource
     *                            the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'name' => $resource->name,
            'locked' => (bool) $resource->locked,
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
            'parent' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['parent']),
                self::DATA => function () use ($item) {
                    return $item->parent;
                },
            ],
            'tags' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['tags']),
                self::DATA => function () use ($item) {
                    return $item->tags ? $item->tags : [];
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
            'roles' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['roles']),
                self::DATA => function () use ($item) {
                    return $item->roles ? $item->roles : [];
                },
            ],
            'users' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['users']),
                self::DATA => function () use ($item) {
                    return $item->users ? $item->users : [];
                },
            ],
            'files' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['files']),
                self::DATA => function () use ($item) {
                    return $item->files ? $item->files : [];
                },
            ],
            'subfolders' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['subfolders']),
                self::DATA => function () use ($item) {
                    return $item->subfolders ? $item->subfolders : [];
                },
            ],
            'owner' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['owner']),
                self::DATA => function () use ($item) {
                    return $item->owner;
                },
            ],
        ];
    }
}
