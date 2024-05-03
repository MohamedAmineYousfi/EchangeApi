<?php

namespace App\JsonApi\V1\SalesPayments;

use App\Models\SalesPayment;
use App\Rules\SalesPaymentInvoiceAmount;
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
        'organization',
        'allowedLocations',
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
        'organization',
        'allowedLocations',
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
        if ($record) {
            if ($record->status != SalesPayment::STATUS_DRAFT) {
                abort(400, 'CANNOT_UPDATE_PAYMENT_NOT_DRAFT');
            }
        }

        return [
            'source' => [
                'required',
                'in:'.
                    SalesPayment::SOURCE_MANUAL.','.
                    SalesPayment::SOURCE_STRIPE.','.
                    SalesPayment::SOURCE_PAYPAL.','.
                    SalesPayment::SOURCE_CASH.','.
                    SalesPayment::SOURCE_UNKNOWN,
            ],
            'status' => [
                'required',
                'in:'.
                    SalesPayment::STATUS_DRAFT.','.
                    SalesPayment::STATUS_COMPLETED.','.
                    SalesPayment::STATUS_CANCELED,
            ],
            'amount' => ['required', 'numeric', new SalesPaymentInvoiceAmount()],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'transaction_id' => ['sometimes', 'nullable', 'string'],
            'transaction_data' => ['sometimes', 'nullable', 'string'],
            'invoice' => [
                'required',
                new HasOne('sales-invoices'),
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
