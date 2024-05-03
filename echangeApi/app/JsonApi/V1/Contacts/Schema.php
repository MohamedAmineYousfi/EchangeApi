<?php

namespace App\JsonApi\V1\Contacts;

use App\Models\Contact;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'contacts';

    /**
     * @param  Contact  $resource
     *                             the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Contact  $resource
     *                             the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'firstname' => $resource->firstname,
            'lastname' => $resource->lastname,
            'company_name' => $resource->company_name,
            'title' => $resource->title,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'birthday' => $resource->birthday,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'phone_extension' => $resource->phone_extension,
            'phone_type' => $resource->phone_type,
            'other_phones' => $resource->other_phones ? $resource->other_phones : [],
            'country' => $resource->country,
            'state' => $resource->state,
            'city' => $resource->city,
            'zipcode' => $resource->zipcode,
            'address' => $resource->address,
            'properties' => $resource->properties,
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
            'contactable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['contactable']),
                self::DATA => function () use ($item) {
                    return $item->contactable;
                },
            ], /*
            'tags' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['tags']),
                self::DATA => function () use ($item) {
                    return $item->tags ? $item->tags : [];
                },
            ],*/
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
