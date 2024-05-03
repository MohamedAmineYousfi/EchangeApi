<?php

namespace App\JsonApi\V1\PurchasesPayments;

use App\Models\PurchasesPayment;
use App\Rules\PurchasesPaymentInvoiceAmount;
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
            if ($record->status != PurchasesPayment::STATUS_DRAFT) {
                abort(400, 'CANNOT_UPDATE_PAYMENT_NOT_DRAFT');
            }
        }

        return [
            'source' => [
                'required',
                'in:'.
                    PurchasesPayment::SOURCE_MANUAL.','.
                    PurchasesPayment::SOURCE_STRIPE.','.
                    PurchasesPayment::SOURCE_PAYPAL.','.
                    PurchasesPayment::SOURCE_CASH.','.
                    PurchasesPayment::SOURCE_UNKNOWN,
            ],
            'status' => [
                'required',
                'in:'.
                    PurchasesPayment::STATUS_DRAFT.','.
                    PurchasesPayment::STATUS_COMPLETED.','.
                    PurchasesPayment::STATUS_CANCELED,
            ],
            'amount' => ['required', 'numeric', new PurchasesPaymentInvoiceAmount()],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'transaction_id' => ['sometimes', 'nullable', 'string'],
            'transaction_data' => ['sometimes', 'nullable', 'string'],
            'invoice' => [
                'required',
                new HasOne('purchases-invoices'),
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
