<?php

namespace App\JsonApi\V1\Taxes;

use App\Models\Tax;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'taxes';

    /**
     * @param  Tax  $resource
     *                         the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Tax  $resource
     *                         the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'active' => boolval($resource->active),
            'name' => $resource->name,
            'label' => $resource->label,
            'tax_number' => $resource->tax_number,
            'tax_type' => $resource->tax_type,
            'calculation_type' => $resource->calculation_type,
            'calculation_base' => $resource->calculation_base,
            'value' => $resource->value,
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
        ];
    }
}
