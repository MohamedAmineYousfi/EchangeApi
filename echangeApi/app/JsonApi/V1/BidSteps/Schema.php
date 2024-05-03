<?php

namespace App\JsonApi\V1\BidSteps;

use App\Models\BidStep;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'bid-steps';

    /**
     * @param  BidStep  $resource
     *                             the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  BidStep  $resource
     *                             the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'amount_min' => $resource->amount_min,
            'amount_max' => $resource->amount_max,
            'bid_amount' => $resource->bid_amount,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
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
            'auction' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['auction']),
                self::DATA => function () use ($item) {
                    return $item->auction ?? [];
                },
            ],
        ];
    }
}
