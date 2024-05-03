<?php

namespace App\Http\Requests\Api\V1\Property;

use App\Constants\AuctionInformation;
use App\Constants\Permissions;
use App\Constants\PropertyInformation;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionPropertyFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var ?Property $record */
        $record = $this->route('record');
        if (! $record) {
            return false;
        }

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_CREATE_TRANSACTIONS_PROPERTIES);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'transaction_excerpt' => ['nullable', 'string'],
            'customer' => ['required', 'string', 'min:5',],
            'transactions' => ['required', 'array'],
            'transactions.*.amount' => ['sometimes', 'numeric', 'gt:0'],
            'transactions.*.transaction_type' => ['sometimes', 'string', 'in:' . implode(',', PropertyInformation::PAYMENTS_TYPE_LIST)],
            'transactions.*.method_payment' => ['sometimes', 'string', 'in:' . implode(',', AuctionInformation::AUCTION_PAYMENTS)],
        ];
    }
}
