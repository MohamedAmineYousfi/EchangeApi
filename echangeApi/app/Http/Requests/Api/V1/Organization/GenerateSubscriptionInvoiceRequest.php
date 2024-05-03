<?php

namespace App\Http\Requests\Api\V1\Organization;

use App\Constants\Permissions;
use App\Models\Organization;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

class GenerateSubscriptionInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var Organization */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_ORGANIZATIONS);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Organization */
        $organization = $this->route('record');

        return [
            'package' => [
                'required',
                'exists:packages,id',
                function ($attribute, $value, $fail) use ($organization) {
                    /** @var Builder */
                    $activeSubscriptions = Subscription::whereDate('end_time', '>=', Carbon::now())
                        ->where('organization_id', '=', $organization->id)
                        ->where('package_id', '=', $value);
                    if ($activeSubscriptions->count() > 0) {
                        $fail('Cannot add subscription, this organization has an active subscription for this package');
                    }
                },
            ],
        ];
    }
}
