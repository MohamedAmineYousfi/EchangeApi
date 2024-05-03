<?php

namespace App\JsonApi\V1\Suppliers;

use App\Models\Supplier;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'suppliers';

    /**
     * @param  Supplier  $resource
     *                              the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Supplier  $resource
     *                              the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'company_name' => $resource->company_name,
            'fiscal_number' => $resource->fiscal_number,
            'tags' => $resource->tags,
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
            'excerpt' => $resource->excerpt,
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
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->tags;
                },
            ],
            'allowedLocations' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['allowedLocations']),
                self::DATA => function () use ($item) {
                    return $item->allowedLocations ? $item->allowedLocations : [];
                },
            ],
            'imports' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['imports']),
                self::DATA => function () use ($item) {
                    return $item->imports;
                },
            ],
        ];
    }
}
