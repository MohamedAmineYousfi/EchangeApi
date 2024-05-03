<?php

namespace App\JsonApi\V1\PurchasesInvoiceItems;

use App\Models\UnitOfMeasureUnit;
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
        'unitOfMeasure',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'name',
        'unit_type',
        'ratio',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'name',
        'unit_type',
        'ratio',
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
            'name' => ['required', 'string', 'min:3', 'max:128'],
            'unit_type' => [
                'required', 'in:'.
                    UnitOfMeasureUnit::TYPE_BIGGER_THAN_REFERENCE.','.
                    UnitOfMeasureUnit::TYPE_SMALLER_THAN_REFERENCE.','.
                    UnitOfMeasureUnit::TYPE_REFERENCE_UNIT,
            ],
            'ratio' => ['required', 'numeric'],
            'unitOfMeasure' => [
                'required',
                new HasOne('unit-of-measures'),
            ],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [];
    }
}
