<?php

namespace App\JsonApi\V1\Files;

use App\Helpers\File as HelpersFile;
use App\Models\File;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'files';

    /**
     * @param  File  $resource
     *                          the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  File  $resource
     *                          the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'name' => $resource->name,
            'size' => $resource->size,
            'human_readable_size' => HelpersFile::getHumanReadableSize($resource->size),
            'url' => URL::asset(Storage::url($resource->path), true),
            'path' => $resource->path,
            'file_extension' => FacadesFile::extension($resource->path),
            'file_name' => basename($resource->name, '.'.FacadesFile::extension($resource->path)),
            'file_history' => $resource->file_history ? $resource->file_history : [],
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
            'object' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['object']),
                self::DATA => function () use ($item) {
                    return $item->object;
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
