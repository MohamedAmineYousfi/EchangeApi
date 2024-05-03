<?php

namespace App\Http\Requests\Api\V1\SalesOrder;

use App\Constants\Permissions;
use App\Models\SalesInvoice;
use Illuminate\Foundation\Http\FormRequest;

class SalesOrderPrintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var SalesInvoice */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_VIEW_SALES_ORDERS);
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
