<?php

namespace App\JsonApi\V1\PurchasesOrders;

use App\Models\PurchasesOrder;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'purchases-orders';

    /**
     * @param  PurchasesOrder  $resource
     *                                    the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  PurchasesOrder  $resource
     *                                    the domain record being serialized.
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
                'has_no_taxes' => (bool) $resource->has_no_taxes,
                'invoicing_status' => $resource->invoicing_status,
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
            'issuer' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->issuer;
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
            'destinationWarehouse' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->destinationWarehouse;
                },
            ],
        ];
    }
}
