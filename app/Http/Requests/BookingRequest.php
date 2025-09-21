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
        
        $rules = [
            'retreat_id' => 'required|exists:retreats,id',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'age' => 'required|integer|min:1|max:120',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'congregation' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'additional_participants' => 'required|integer|min:0|max:' . $maxAdditionalMembers,
            'special_remarks' => 'nullable|string',
            'participants' => 'sometimes|array|max:' . $maxAdditionalMembers,
            'participants.*.firstname' => 'required_with:participants|string|max:255',
            'participants.*.lastname' => 'required_with:participants|string|max:255',
            'participants.*.whatsapp_number' => 'required_with:participants|string|max:20',
            'participants.*.age' => 'required_with:participants|integer|min:1|max:120',
            'participants.*.email' => 'required_with:participants|email|max:255',
            'participants.*.gender' => 'required_with:participants|in:male,female,other',
        ];

        // Add validation for additional participants if any
        $additionalParticipants = (int) $this->input('additional_participants', 0);
        if ($additionalParticipants > 0) {
            $rules['participants'] = 'required|array|size:' . $additionalParticipants;
            
            for ($i = 0; $i < $additionalParticipants; $i++) {
                $rules["participants.{$i}.firstname"] = 'required|string|max:255';
                $rules["participants.{$i}.lastname"] = 'required|string|max:255';
                $rules["participants.{$i}.whatsapp_number"] = 'required|string|max:20';
                $rules["participants.{$i}.age"] = 'required|integer|min:1|max:120';
                $rules["participants.{$i}.email"] = 'required|email|max:255';
                $rules["participants.{$i}.gender"] = 'required|in:male,female,other';
            }
        }

        return $rules;
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
