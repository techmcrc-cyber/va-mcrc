<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RetreatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $retreatId = $this->route('retreat');
        $retreatId = $retreatId instanceof \App\Models\Retreat ? $retreatId->id : $retreatId;
        
        // Check if this is an update (edit) or create request
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH') || $retreatId;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('retreats', 'slug')->ignore($retreatId),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
            ],
            'description' => ['required', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'start_date' => $isUpdate 
                ? ['required', 'date'] 
                : ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'timings' => ['required', 'string', 'max:255'],
            'seats' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'criteria' => [
                'required',
                Rule::in([
                    'male_only',
                    'female_only',
                    'priests_only',
                    'sisters_only',
                    'youth_only',
                    'children',
                    'no_criteria'
                ])
            ],
            'whatsapp_channel_link' => ['required', 'url', 'max:500'],
            'special_remarks' => ['nullable', 'string'],
            'instructions' => ['required', 'string'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain letters, numbers, and hyphens, and must not begin or end with a hyphen.',
            'end_date.after' => 'The end date must be after the start date.',
            'discount_price.lt' => 'The discount price must be less than the regular price.',
            'featured_image.max' => 'The featured image must not be larger than 2MB.',
            'gallery.*.max' => 'Each image in the gallery must not be larger than 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Generate slug from title if not provided
        if ($this->has('title') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title . ' ' . now()->format('Y-m-d'))
            ]);
        }

        // Ensure boolean fields are properly cast
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
