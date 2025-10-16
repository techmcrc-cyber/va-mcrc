<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'need' => 'required|in:retreat,custom',
            'retreat_id' => 'required_if:need,retreat|nullable|exists:retreats,id',
            'heading' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'greeting' => 'nullable|string|max:255',
            'additional_users_emails' => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate additional emails format
            if ($this->additional_users_emails) {
                $emails = array_map('trim', explode(',', $this->additional_users_emails));
                foreach ($emails as $email) {
                    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $validator->errors()->add('additional_users_emails', "The email '{$email}' is not valid.");
                    }
                }
            }

            // Ensure at least one recipient exists
            if ($this->need === 'custom' && empty(trim($this->additional_users_emails ?? ''))) {
                $validator->errors()->add('additional_users_emails', 'At least one email address is required for custom notifications.');
            }

            // Validate retreat is active
            if ($this->need === 'retreat' && $this->retreat_id) {
                $retreat = \App\Models\Retreat::find($this->retreat_id);
                if ($retreat && !$retreat->is_active) {
                    $validator->errors()->add('retreat_id', 'The selected retreat is not active.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'need.required' => 'Please select a notification type.',
            'need.in' => 'Invalid notification type selected.',
            'retreat_id.required_if' => 'Please select a retreat.',
            'retreat_id.exists' => 'The selected retreat does not exist.',
            'subject.required' => 'Please enter a subject.',
            'body.required' => 'Please enter the email body.',
        ];
    }
}
