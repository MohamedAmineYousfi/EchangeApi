<?php

namespace App\Http\Requests\Api\V1\Location;

use App\Constants\Permissions;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class UserAddToLocationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var ?Location $record */
        $record = $this->route('record');
        if (! $record) {
            return false;
        }

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_LOCATIONS);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'users' => ['required', 'array'],
            'users.*' => ['required', 'exists:users,id'],
        ];
    }
}
