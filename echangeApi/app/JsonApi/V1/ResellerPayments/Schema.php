<?php

namespace App\JsonApi\V1\ResellerPayments;

use App\Models\ResellerPayment;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'reseller-payments';

    /**
     * @param  ResellerPayment  $resource
     *                                     the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  ResellerPayment  $resource
     *                                     the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'code' => $resource->code,
            'date' => $resource->date,
            'status' => $resource->status,
            'source' => $resource->source,
            'amount' => $resource->amount,
            'transaction_id' => $resource->transaction_id,
            'transaction_data' => json_encode($resource->transaction_data),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'invoice' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->invoice;
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
        ];
    }
}
