<?php

namespace App\JsonApi\V1\Tags;

use App\Models\Tag;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'tags';

    /**
     * @param  Tag  $resource
     *                         the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Tag  $resource
     *                         the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'name' => $resource->name,
            'slug' => $resource->slug,
            'color' => $resource->color ? $resource->color : '#11cdef',
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships): array
    {
        return [];
    }
}
