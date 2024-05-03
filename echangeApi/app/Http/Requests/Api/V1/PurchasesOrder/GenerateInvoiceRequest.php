<?php

namespace App\Http\Requests\Api\V1\PurchasesOrder;

use App\Constants\Permissions;
use App\Models\PurchasesOrder;
use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var PurchasesOrder */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_PURCHASES_ORDERS);
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
