<?php

namespace App\JsonApi\V1\TaxGroups;

use App\Models\TaxGroup;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'tax-groups';

    /**
     * @param  TaxGroup  $resource
     *                              the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  TaxGroup  $resource
     *                              the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'active' => boolval($resource->active),
            'name' => $resource->name,
            'country_code' => $resource->country_code,
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
            'taxes' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['taxes']),
                self::DATA => function () use ($item) {
                    return $item->taxes;
                },
            ],
        ];
    }
}
