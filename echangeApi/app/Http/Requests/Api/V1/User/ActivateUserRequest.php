<?php

namespace App\Http\Requests\Api\V1\User;

use App\Constants\Permissions;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ActivateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var ?User $record */
        $record = $this->route('record');
        if (! $record) {
            return false;
        }

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_USERS);
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
