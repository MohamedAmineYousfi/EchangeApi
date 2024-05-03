<?php

namespace App\JsonApi\V1\Packages;

use App\Models\Package;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'packages';

    /**
     * @param  Package  $resource
     *                             the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Package  $resource
     *                             the domain record being serialized.
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
            'frequency' => $resource->frequency,
            'maximum_users' => $resource->maximum_users,
            'denomination' => $resource->getDenomination(),
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * getRelationships
     *
     * @param  mixed  $ressource
     * @param  mixed  $isPrimary
     * @return array
     */
    public function getRelationships($ressource, $isPrimary, array $includeRelationships)
    {
        return [
            'reseller' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['reseller']),
                self::DATA => function () use ($ressource) {
                    return $ressource->reseller;
                },
            ],
            'default_role' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['default_role']),
                self::DATA => function () use ($ressource) {
                    return $ressource->defaultRole;
                },
            ],
            'taxGroups' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['taxGroups']),
                self::DATA => function () use ($ressource) {
                    return $ressource->taxGroups;
                },
            ],
        ];
    }
}
