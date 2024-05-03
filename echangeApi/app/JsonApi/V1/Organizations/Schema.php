<?php

namespace App\JsonApi\V1\Organizations;

use App\Models\Organization;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'organizations';

    /**
     * @param  Organization  $resource
     *                                  the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Organization  $resource
     *                                  the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'email' => $resource->email,
            'address' => $resource->address,
            'phone' => $resource->phone,
            'phone_extension' => $resource->phone_extension,
            'phone_type' => $resource->phone_type,
            'other_phones' => $resource->other_phones ? $resource->other_phones : [],
            'logo' => $resource->logo,
            'reseller_id' => $resource->reseller->id,
            'activePermissions' => $resource->getActivePermissions()->pluck('key'),
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),

            ...$resource->getBillingInformations(),
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'owner' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['owner']),
                self::DATA => function () use ($item) {
                    return $item->owner;
                },
            ],
            'reseller' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['reseller']),
                self::DATA => function () use ($item) {
                    return $item->reseller;
                },
            ],
            'subscriptions' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['subscriptions']),
                self::DATA => function () use ($item) {
                    return $item->subscriptions;
                },
            ],
            'activeSubscriptions' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['activeSubscriptions']),
                self::DATA => function () use ($item) {
                    return $item->activeSubscriptions;
                },
            ],
        ];
    }
}
