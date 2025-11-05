<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
        $maxAdditionalMembers = config('bookings.max_additional_members', 3);
        
        // Get the selected retreat to check criteria
        $retreat = null;
        if ($this->has('retreat_id')) {
            $retreat = \App\Models\Retreat::find($this->input('retreat_id'));
        }
        
        // Determine if congregation is required based on retreat criteria
        $congregationRule = 'nullable|string|max:255';
        if ($retreat && in_array($retreat->criteria, ['priests_only', 'sisters_only'])) {
            $congregationRule = 'required|string|max:255';
        }
        
        $rules = [
            'retreat_id' => 'required|exists:retreats,id',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|min:7|max:15|regex:/^[0-9]+$/',
            'country_code' => 'required|string|max:10',
            'age' => 'required|integer|min:1|max:120',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'married' => 'nullable|in:yes,no',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'congregation' => $congregationRule,
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'additional_participants' => 'required|integer|min:0|max:' . $maxAdditionalMembers,
            'special_remarks' => 'nullable|string',
        ];

        // Add validation for additional participants if any
        $additionalParticipants = (int) $this->input('additional_participants', 0);
        
        if ($additionalParticipants > 0) {
            $rules['participants'] = 'required|array|min:1|max:' . $maxAdditionalMembers;
            
            foreach ($this->input('participants', []) as $index => $participant) {
                $rules["participants.{$index}.firstname"] = 'required|string|max:255';
                $rules["participants.{$index}.lastname"] = 'required|string|max:255';
                $rules["participants.{$index}.whatsapp_number"] = 'required|string|min:7|max:15|regex:/^[0-9]+$/';
                $rules["participants.{$index}.country_code"] = 'required|string|max:10';
                $rules["participants.{$index}.age"] = 'required|integer|min:1|max:120';
                $rules["participants.{$index}.email"] = 'required|email|max:255';
                $rules["participants.{$index}.gender"] = 'required|in:male,female,other';
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'congregation.required' => 'The congregation field is required for Priests and Sisters retreats.',
            'whatsapp_number.min' => 'WhatsApp number must be between 7 and 15 digits.',
            'whatsapp_number.max' => 'WhatsApp number must be between 7 and 15 digits.',
            'whatsapp_number.regex' => 'WhatsApp number must contain only digits.',
            'participants.*.whatsapp_number.min' => 'WhatsApp number must be between 7 and 15 digits.',
            'participants.*.whatsapp_number.max' => 'WhatsApp number must be between 7 and 15 digits.',
            'participants.*.whatsapp_number.regex' => 'WhatsApp number must contain only digits.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'participants.*.firstname' => 'first name',
            'participants.*.lastname' => 'last name',
            'participants.*.whatsapp_number' => 'WhatsApp number',
            'participants.*.age' => 'age',
            'participants.*.email' => 'email',
            'participants.*.gender' => 'gender',
        ];

        return $attributes;
    }
}
