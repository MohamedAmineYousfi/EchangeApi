<?php

namespace App\JsonApi\V1\ResellerInvoices;

use App\Models\ResellerInvoice;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'reseller-invoices';

    /**
     * @param  ResellerInvoice  $resource
     *                                     the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  ResellerInvoice  $resource
     *                                     the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'code' => $resource->code,
            'expiration_time' => optional($resource->expiration_time)->format('Y-m-d H:i:s'),
            'status' => $resource->status,
            'pricing' => [
                'taxes' => $resource->getInvoiceTaxes(),
                'discounts' => $resource->getInvoiceDiscounts(),
                'subtotal' => $resource->getInvoiceSubTotalAmount(),
                'total' => $resource->getInvoiceTotalAmount(),
            ],
            'total_paied' => $resource->getInvoiceTotalPaied(),
            'total_remaining_payment' => $resource->getInvoiceTotalAmount() - $resource->getInvoiceTotalPaied(),
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
            'payments' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['payments']),
                self::DATA => function () use ($item) {
                    return $item->payments;
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
