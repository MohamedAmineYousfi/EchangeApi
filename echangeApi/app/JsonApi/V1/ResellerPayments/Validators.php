<?php

namespace App\JsonApi\V1\ResellerPayments;

use App\Models\ResellerPayment;
use App\Rules\ResellerPaymentInvoiceAmount;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;

class Validators extends AbstractValidators
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *                    the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = [
        'invoice',
        'reseller',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'code',
        'date',
        'created_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'code',
        'date',
        'created_at',
        'invoice',
        'status',
        'reseller',
    ];

    /**
     * Get resource validation rules.
     *
     * @param  mixed|null  $record
     *                              the record being updated, or null if creating a resource.
     * @param  array  $data
     *                       the data being validated
     */
    protected function rules($record, array $data): array
    {
        return [
            'source' => ['required', 'in:'.ResellerPayment::SOURCE_MANUAL.','.ResellerPayment::SOURCE_STRIPE.','.ResellerPayment::SOURCE_PAYPAL.','.ResellerPayment::SOURCE_CASH.','.ResellerPayment::SOURCE_UNKNOWN],
            'status' => ['required', 'in:'.ResellerPayment::STATUS_DRAFT.','.ResellerPayment::STATUS_COMPLETED.','.ResellerPayment::STATUS_CANCELED],
            'amount' => ['required', 'numeric', new ResellerPaymentInvoiceAmount()],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'transaction_id' => ['sometimes', 'nullable', 'string'],
            'transaction_data' => ['sometimes', 'nullable', 'string'],
            'invoice' => [
                'required',
                new HasOne('reseller-invoices'),
            ],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.invoice' => 'string',
            'filter.date' => 'array|min:2',
            'filter.date.*' => 'filled|date_format:Y-m-d H:i:s',
            'filter.created_at' => 'array|min:2',
            'filter.created_at.*' => 'filled|date_format:Y-m-d H:i:s',
            'filter.code' => 'string',
            'filter.status' => 'string',
        ];
    }
}
