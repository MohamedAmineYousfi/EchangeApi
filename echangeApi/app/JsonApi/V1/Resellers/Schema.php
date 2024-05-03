<?php

namespace App\JsonApi\V1\Resellers;

use App\Models\Reseller;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'resellers';

    /**
     * @param  Reseller  $resource
     *                              the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Reseller  $resource
     *                              the domain record being serialized.
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
            'config_manager_app_name' => $resource->config_manager_app_name,
            'config_manager_app_logo' => $resource->config_manager_app_logo,
            'config_manager_url_regex' => $resource->config_manager_url_regex,
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * getRelationships
     *
     * @param  mixed  $item
     * @param  bool  $isPrimary
     * @param  array  $includeRelationships
     * @return array
     */
    public function getRelationships($item, $isPrimary, $includeRelationships)
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
        ];
    }
}
