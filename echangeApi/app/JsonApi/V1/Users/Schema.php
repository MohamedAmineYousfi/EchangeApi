<?php

namespace App\JsonApi\V1\Users;

use App\Models\User;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'users';

    /**
     * @param  object  $resource
     *                            the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  object  $resource
     *                            the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'active' => $resource->active,
            'firstname' => $resource->firstname,
            'lastname' => $resource->lastname,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'phone_extension' => $resource->phone_extension,
            'phone_type' => $resource->phone_type,
            'other_phones' => $resource->other_phones ? $resource->other_phones : [],
            'locale' => $resource->locale,
            'is_staff' => $resource->is_staff,
            'two_fa_code' => $resource->two_fa_code,
            'two_fa_enabled_at' => $resource->two_fa_enabled_at,
            'two_fa_disabled_at' => $resource->two_fa_disabled_at,
            'is_2fa_enabled' => (bool) $resource->is_2fa_enabled,
            'verification_code_expires_at' => $resource->verification_code_expires_at,
            'profile_image' => $resource->profile_image,
            'restrict_to_locations' => $resource->restrict_to_locations,
            ...$resource->getBillingInformations(),
            'created_at' => optional($resource->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($resource->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * getRelationships
     *
     * @param  User  $item
     * @param  mixed  $isPrimary
     * @param  array<string, string>  $includeRelationships
     * @return array
     */
    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'roles' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['roles']),
                self::DATA => function () use ($item) {
                    return $item->roles;
                },
            ],
            'organization' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
            'reseller' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['reseller']),
                self::DATA => function () use ($item) {
                    return $item->reseller;
                },
            ],
            'allowedLocations' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['allowedLocations']),
                self::DATA => function () use ($item) {
                    return $item->allowedLocations ? $item->allowedLocations : [];
                },
            ],
        ];
    }
}
