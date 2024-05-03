<?php

namespace App\JsonApi\V1\SalesOrders;

use App\Models\SalesOrder;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'sales-orders';

    /**
     * @param  SalesOrder  $resource
     *                                the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  SalesOrder  $resource
     *                                the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        $isSingleRequest = (bool) app('json-api')->currentRoute()->getResourceId();
        if ($isSingleRequest) {
            return [
                'excerpt' => $resource->excerpt ? $resource->excerpt : '',
                'code' => $resource->code,
                'expiration_time' => optional($resource->expiration_time)->format('Y-m-d H:i:s'),
                'invoicing_type' => $resource->invoicing_type,
                'status' => $resource->status,
                'invoicing_status' => $resource->invoicing_status,
                'has_no_taxes' => (bool) $resource->has_no_taxes,
                'delivery_status' => $resource->delivery_status,
                'pricing' => [
                    'taxes' => $resource->getOrderTaxes(),
                    'discounts' => $resource->getOrderDiscounts(),
                    'subtotal' => $resource->getOrderSubTotalAmount(),
                    'total' => $resource->getOrderTotalAmount(),
                ],
                'deliveryItemsState' => $resource->getDeliveryItemsState(),
                'invoicingItemsState' => $resource->getInvoicingItemsState(),
                'invoicingAmountsState' => $resource->getInvoicingAmountsState(),
                'created_at' => $resource->created_at,
                'updated_at' => $resource->updated_at,

                ...$resource->getBillingInformations(),
            ];
        }

        return [
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'code' => $resource->code,
            'expiration_time' => optional($resource->expiration_time)->format('Y-m-d H:i:s'),
            'status' => $resource->status,
            'invoicing_status' => $resource->invoicing_status,
            'delivery_status' => $resource->delivery_status,
            'pricing' => [
                'taxes' => $resource->getOrderTaxes(),
                'discounts' => $resource->getOrderDiscounts(),
                'subtotal' => $resource->getOrderSubTotalAmount(),
                'total' => $resource->getOrderTotalAmount(),
            ],
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,

            ...$resource->getBillingInformations(),
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'items' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['items']),
                self::DATA => function () use ($item) {
                    return $item->items;
                },
            ],
            'recipient' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->recipient;
                },
            ],
            'organization' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['organization']),
                self::DATA => function () use ($item) {
                    return $item->organization;
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
            'sourceWarehouse' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->sourceWarehouse;
                },
            ],
        ];
    }
}
