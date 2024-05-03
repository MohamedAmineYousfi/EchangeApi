<?php

namespace App\JsonApi\V1\Properties;

use App\Models\Property;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'properties';

    /**
     * @param  Property  $resource
     *                              the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  Property  $resource
     *                              the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource): array
    {
        return [
            'status' => $resource->status,
            'auction_is_closed' => $resource->getAuctionIsClosed(),
            'sold_amount' => $resource->sold_amount,
            'sold_at' => $resource->sold_at,
            'approved_at' => $resource->approved_at,
            'sale_confirmed_at' => $resource->sale_confirmed_at,
            'mrc_fees' => $resource->mrc_fees,
            'bid_starting_amount' => $resource->bid_starting_amount,
            'taxable' => (bool) $resource->taxable,
            'designation' => $resource->designation,
            'acquisition_number' => $resource->acquisition_number,
            'acquisition_method' => $resource->acquisition_method,
            'property_number' => $resource->property_number,
            'owed_taxes_municipality' => $resource->owed_taxes_municipality ?? 0.0,
            'owed_taxes_school_board' => $resource->owed_taxes_school_board ?? 0.0,
            'total' => $resource->getTotal(),
            'total_taxes' => $resource->getTotalTaxes(),
            'sub_total' => $resource->getSubtotal(),
            'registration_number' => $resource->registration_number,
            'batch_numbers' => $resource->batch_numbers ?? [],
            'property_type' => $resource->property_type,
            'customer' => $resource->customer,
            'transactions' => $resource->transactions ?? [],
            'transaction_excerpt' => $resource->transaction_excerpt,
            'transaction_date' => $resource->transaction_date,
            'taxes_due' => $resource->taxes_due ?? [],
            'country' => $resource->country,
            'active' => (bool) $resource->active,
            'state' => $resource->state,
            'city' => $resource->city,
            'zipcode' => $resource->zipcode,
            'address' => $resource->address,
            'cancel_reason' => $resource->cancel_reason,
            'excerpt' => $resource->excerpt ?: '',
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
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
            'organization' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
                },
            ],
            'auction' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['auction']),
                self::DATA => function () use ($item) {
                    return $item->auction;
                },
            ],
            'updatedBy' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['updatedBy']),
                self::DATA => function () use ($item) {
                    return $item->updatedBy;
                },
            ],
            'createdBy' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['createdBy']),
                self::DATA => function () use ($item) {
                    return $item->createdBy;
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
            'owners' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['owners']),
                self::DATA => function () use ($item) {
                    return $item->owners ?? [];
                },
            ],
            'paymentReceivedBy' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['paymentReceivedBy']),
                self::DATA => function () use ($item) {
                    return $item->paymentReceivedBy;
                },
            ],
        ];
    }
}
