<?php

namespace App\Http\Requests\Api\V1\Import;

use App\Constants\Permissions;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RunDryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var ?User $record */
        $record = $this->route('record');
        if (! $record) {
            return false;
        }

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_CREATE_IMPORTS);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }
}
