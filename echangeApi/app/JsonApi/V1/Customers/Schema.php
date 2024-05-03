<?php

namespace App\JsonApi\V1\Customers;

use App\Models\Customer;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'customers';

    /**
     * @param  Customer  $resource
     *                              the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Customer  $resource
     *                              the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'customer_type' => $resource->customer_type,
            'firstname' => $resource->firstname,
            'lastname' => $resource->lastname,
            'company_name' => $resource->company_name,
            'customer_name' => $resource->getCustomerName(),
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
            ...$resource->getBillingInformations(),
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
            'contacts' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['contacts']),
                self::DATA => function () use ($item) {
                    return $item->contacts;
                },
            ],
            'tags' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['tags']),
                self::DATA => function () use ($item) {
                    return $item->tags ? $item->tags : [];
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
