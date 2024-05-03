<?php

namespace App\JsonApi\V1\UnitOfMeasureUnits;

use App\Models\UnitOfMeasureUnit;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'unit-of-measure-units';

    /**
     * @param  UnitOfMeasureUnit  $resource
     *                                       the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  UnitOfMeasureUnit  $resource
     *                                       the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'unit_type' => $resource->unit_type,
            'ratio' => $resource->ratio,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'unitOfMeasure' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->unitOfMeasure;
                },
            ],
        ];
    }
}
