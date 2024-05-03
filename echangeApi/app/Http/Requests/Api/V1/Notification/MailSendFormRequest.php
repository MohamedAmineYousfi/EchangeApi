<?php

namespace App\Http\Requests\Api\V1\Notification;

use Illuminate\Foundation\Http\FormRequest;

class MailSendFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $multipleEmailRegex = 'regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:,\s*[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})*,?)$/';

        return [
            'body' => ['nullable', 'string'],
            'subject' => ['sometimes', 'string'],
            'to' => [
                'sometimes',
                'string',
                $multipleEmailRegex,
            ],
            'cc' => [
                'sometimes',
                'string',
                $multipleEmailRegex,
            ],
            'bcc' => [
                'sometimes',
                'string',
                $multipleEmailRegex,
            ],
        ];
    }

    public function messages()
    {
        return [
            'to.regex' => __('errors.multiple_email_validation'),
            'cc.regex' => __('errors.multiple_email_validation'),
            'bcc.regex' => __('errors.multiple_email_validation'),
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->transformEmailFieldToArray($validator, 'to');
            $this->transformEmailFieldToArray($validator, 'cc');
            $this->transformEmailFieldToArray($validator, 'bcc');
        });
    }

    private function transformEmailFieldToArray($validator, $fieldName)
    {
        $value = $this->input($fieldName);
        if ($validator->errors()->has($fieldName) || empty($value)) {
            return;
        }
        $emailArray = explode(',', $value);
        $this->merge([$fieldName => $emailArray]);
    }
}
