<?php

namespace App\Http\Requests\Api\V1\Warehouse;

use App\Constants\Permissions;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Http\FormRequest;

class ApplyConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var ?Warehouse $record */
        $record = $this->route('record');
        if (! $record) {
            return false;
        }

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_CREATE_WAREHOUSES);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }
}
