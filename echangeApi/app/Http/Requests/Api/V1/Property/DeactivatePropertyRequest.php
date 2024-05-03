<?php

namespace App\Http\Requests\Api\V1\Property;

use App\Constants\Permissions;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class DeactivatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var ?Property $record */
        $record = $this->route('record');
        if (! $record) {
            return false;
        }

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_PROPERTIES);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
