<?php

namespace App\JsonApi\V1\UnitOfMeasures;

use App\Models\UnitOfMeasure;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'unit-of-measures';

    /**
     * @param  UnitOfMeasure  $resource
     *                                   the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  UnitOfMeasure  $resource
     *                                   the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'units' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->units;
                },
            ],
            'referenceUnit' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->getReferenceUnit();
                },
            ],
            'organization' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
        ];
    }
}
