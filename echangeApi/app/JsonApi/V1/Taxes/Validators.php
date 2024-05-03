<?php

namespace App\JsonApi\V1\Taxes;

use App\Models\Tax;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;
use Illuminate\Validation\Rule;

class Validators extends AbstractValidators
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *                    the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = [
        'organization',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'name',
        'label',
        'tax_number',
        'tax_type',
        'calculation_type',
        'calculation_base',
        'value',
        'created_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'name',
        'label',
        'tax_number',
        'tax_type',
        'calculation_type',
        'calculation_base',
        'value',
        'organization',
        'id',
        'search',
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
            'active' => ['required', 'bool'],
            'name' => ['required', 'string', 'min:3', 'max:128'],
            'label' => ['nullable', 'string', 'min:3', 'max:128'],
            'tax_number' => ['nullable', 'string', 'min:3', 'max:128'],
            'tax_type' => [
                'required',
                Rule::in([
                    Tax::TAX_TYPE_SALES,
                    Tax::TAX_TYPE_PURCHASES,
                ]),
            ],
            'calculation_type' => [
                'required',
                Rule::in([
                    Tax::TAX_CALCULATION_TYPE_AMOUNT,
                    Tax::TAX_CALCULATION_TYPE_PERCENTAGE,
                ]),
            ],
            'calculation_base' => [
                'required',
                Rule::in([
                    Tax::TAX_CALCULATION_BASE_AFTER_TAX,
                    Tax::TAX_CALCULATION_BASE_BEFORE_TAX,
                ]),
            ],
            'value' => ['required', 'numeric'],
            'excerpt' => ['nullable', 'string'],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.created_at' => 'array|min:2',
            'filter.created_at.*' => 'filled|date_format:Y-m-d H:i:s',
            'filter.organization' => 'string',
            'filter.search' => 'string',
        ];
    }
}
