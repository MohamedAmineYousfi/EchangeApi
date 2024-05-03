<?php

namespace App\Http\Requests\Api\V1\Subscription;

use App\Constants\Permissions;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class RenewSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var Subscription */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_SUBSCRIPTIONS);
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
