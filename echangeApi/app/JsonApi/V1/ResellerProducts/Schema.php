<?php

namespace App\JsonApi\V1\ResellerProducts;

use App\Models\ResellerProduct;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'reseller-products';

    /**
     * @param  ResellerProduct  $resource
     *                                     the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  ResellerProduct  $resource
     *                                     the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'code' => $resource->code,
            'name' => $resource->name,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'price' => $resource->price,
            'picture' => $resource->picture,
            'gallery' => $resource->gallery,
            'denomination' => $resource->getDenomination(),
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * getRelationships
     *
     * @param  mixed  $item
     * @param  mixed  $isPrimary
     * @param  array  $includeRelationships
     * @return array
     */
    public function getRelationships($item, $isPrimary, $includeRelationships)
    {
        return [
            'reseller' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['reseller']),
                self::DATA => function () use ($item) {
                    return $item->reseller;
                },
            ],
        ];
    }
}
