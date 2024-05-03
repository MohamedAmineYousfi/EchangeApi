<?php

namespace App\JsonApi\V1\Imports;

use App\Helpers\ModelClass;
use App\Models\Import;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'imports';

    /**
     * @param  Import  $resource
     *                            the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Import  $resource
     *                            the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'model' => $resource->model,
            'name' => $resource->name,
            'excerpt' => $resource->excerpt ?? '',
            'mapping' => $resource->mapping,
            'file_url' => $resource->file_url,
            'results' => $resource->results,
            'status' => $resource->status,
            'identifier' => $resource['identifier'],
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
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
            'importedItems' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['importedItems']),
                self::DATA => function () use ($item) {
                    return $item->getImportedItems(ModelClass::getModelInstanceByName($item->model))->get();
                },
            ],
            'linkedObject' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['linkedObject']),
                self::DATA => function () use ($item) {
                    return $item->linked_object ?? null;
                },
            ],
        ];
    }
}
