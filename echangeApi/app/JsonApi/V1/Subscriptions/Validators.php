<?php

namespace App\JsonApi\V1\Subscriptions;

use CloudCreativity\LaravelJsonApi\Rules\DateTimeIso8601;
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
    protected $allowedIncludePaths = ['organization', 'package'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = ['created_at', 'start_time', 'end_time', 'code'];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = ['organization', 'package', 'code', 'start_time', 'end_time'];

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
            'start_time' => ['required', new DateTimeIso8601()],
            'end_time' => ['required', new DateTimeIso8601()],
            'package' => ['required', new HasOne('packages')],
            'organization' => ['required', new HasOne('organizations')],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.start_time' => 'array|min:2',
            'filter.start_time.*' => 'filled|date_format:Y-m-d H:i:s',
            'filter.end_time' => 'array|min:2',
            'filter.end_time.*' => 'filled|date_format:Y-m-d H:i:s',
            'filter.code' => 'filled|string',
            'filter.package' => 'filled|string',
            'filter.organization' => 'filled|string',
        ];
    }
}
