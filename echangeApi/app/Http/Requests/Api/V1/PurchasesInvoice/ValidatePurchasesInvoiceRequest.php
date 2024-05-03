<?php

namespace App\Http\Requests\Api\V1\PurchasesInvoice;

use App\Constants\Permissions;
use App\Models\PurchasesInvoice;
use Illuminate\Foundation\Http\FormRequest;

class ValidatePurchasesInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var PurchasesInvoice */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_PURCHASES_INVOICES);
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
