<?php

namespace App\Http\Requests\Api\V1\ResellerInvoice;

use App\Constants\Permissions;
use App\Models\ResellerInvoice;
use Illuminate\Foundation\Http\FormRequest;

class ValidateResellerInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var ResellerInvoice */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_RESELLER_INVOICES);
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
