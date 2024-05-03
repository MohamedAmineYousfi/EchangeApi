<?php

namespace App\JsonApi\V1\SalesInvoiceItems;

use App\Models\SalesInvoiceItem;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'sales-invoice-items';

    /**
     * @param  SalesInvoiceItem  $resource
     *                                      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  SalesInvoiceItem  $resource
     *                                      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'code' => $resource->code,
            'excerpt' => $resource->excerpt ? $resource->excerpt : '',
            'unit_price' => $resource->unit_price,
            'quantity' => $resource->quantity,
            'discount' => $resource->discount,
            'pricing' => [
                'subtotal' => $resource->getItemSubTotalAmount(),
                'discount_amount' => $resource->getItemDiscountAmount(),
                'taxable_amount' => $resource->getTaxableBaseAmount(),
                'taxes' => $resource->getItemTaxes(),
                'total' => $resource->getItemTotalAmount(),
            ],
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships)
    {
        return [
            'salesInvoice' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->salesInvoice;
                },
            ],
            'salesInvoiceable' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($item) {
                    return $item->salesInvoiceable;
                },
            ],
            'taxGroups' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['taxGroups']),
                self::DATA => function () use ($item) {
                    return $item->taxGroups;
                },
            ],
        ];
    }
}
